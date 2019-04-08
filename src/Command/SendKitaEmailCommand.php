<?php
/**
 * Created by PhpStorm.
 * User: thaotruong
 * Date: 28.09.18
 * Time: 05:03
 */

namespace App\Command;

use App\Entity\Email;
use App\Entity\Kita;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class SendKitaEmailCommand extends Command
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct($name = null, \Swift_Mailer $mailer, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }


    protected function configure()
    {
        $this->setName('kita:email:send')
            ->setDescription('Send email to kita');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->entityManager->getRepository('App:Kita');
        $builder = $repo->createQueryBuilder('k');
        $query = $builder
            ->andWhere('k.address LIKE :address')
            ->andWhere('length(k.email) > :email')
            ->setParameter('address', '%Reinickendorf%')
            ->setParameter('email', 0)
            ->getQuery();

        $count = 0;

        /**
         * @var Kita $kita
         */
        foreach ($query->getResult() as $kita) {

            //$kita->setEmail('truong.t.n.thao@gmail.com');

            $body = "Sehr geehrte {$kita->getName()} Mitarbeiter und Mitarbeiterinnen,
            
Ich habe Ihren Kita an {$kita->getUrl()} gefunden und interessiere mich sehr.             

Ich werde am 26.12.2018 einen kleinen Jungen zur Welt bringen und da ich den Kita-Mangel in Berlin kenne, möchte ich mein Baby dort schon am 1.1.2020 registrieren, wenn er 1 Jahr alt ist. Bitte lassen Sie mich wissen, wenn Sie einen Platz für uns haben. Wenn nicht, könnten Sie bitte mein Kind auf eine Warteliste setzen? Wenn Sie auch Kita-Plätze in anderen Kindergärten haben, lassen Sie es mich bitte auch wissen.

Wenn Sie mich kontaktieren möchten, ist meine Telefonnummer 0157 5438 5595 und mein Mann: 0157 5041 5227. Wir würden uns auch für Ihre Info-Tage/Nächte interessieren, wenn Sie diese halten.

Danke und viele Grüße,
Thao und Leo Lipasti";

            $message = (new \Swift_Message('Auf der Suche nach einem Kita-Platz'))
                ->setFrom('truong.t.n.thao@gmail.com')
                ->setTo($kita->getEmail())
                ->setBody($body);

            $output->writeln('Sending email to ' . $kita->getEmail());

            $this->mailer->send($message);

            $email = new Email();
            $email->setEmail($kita->getEmail())
                ->setBody($body)
                ->setKita($kita->getId())
                ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($email);
            $this->entityManager->flush();

            $count++;
            $output->writeln($count);
        }
    }


}