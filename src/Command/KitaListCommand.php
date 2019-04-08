<?php
/**
 * Created by PhpStorm.
 * User: thaotruong
 * Date: 28.09.18
 * Time: 01:53
 */

namespace App\Command;


use App\Entity\Kita;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KitaListCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct($name = null, ContainerInterface $container)
    {
        parent::__construct($name);
        $this->container = $container;
    }


    protected function configure()
    {
        $this->setName('kita:list')
            ->setDescription('Update the kita database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $driver = new GoutteDriver();
        $session = new Session($driver);
        $session->visit('https://www.berlin.de/sen/jugend/familie-und-kinder/kindertagesbetreuung/kitas/verzeichnis/');
        $page = $session->getPage();
        $page->findById('btnSuchen')->click();
        $links = $session->getPage()->findById('DataList_Kitas')->findAll('css', 'a');
        ksort($links, SORT_DESC);
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->container->get('doctrine')->getManager();
        foreach ($links as $link) {
            /**
             * @var NodeElement $link
             */

            try {
                $url = 'https://www.berlin.de/sen/jugend/familie-und-kinder/kindertagesbetreuung/kitas/verzeichnis/' . $link->getAttribute('href');
                $url = str_replace(' ', '', $url);
                $driver = new GoutteDriver();
                $session = new Session($driver);
                $session->visit($url);
                $kitaPage = $session->getPage();
                $email = $kitaPage->findById('HLinkEMail') ? $kitaPage->findById('HLinkEMail')->getText() : null;
                $name = $kitaPage->findById('lblKitaname') ? $kitaPage->findById('lblKitaname')->getText() : null;
                $address = ($kitaPage->findById('lblStrasse') ? $kitaPage->findById('lblStrasse')->getText() : '') . ', ' .
                    ($kitaPage->findById('lblOrt') ? $kitaPage->findById('lblOrt')->getText() : '');
                $phone = $kitaPage->findById('lblTelefon') ? $kitaPage->findById('lblTelefon')->getText() : null;

                if (!$name || !$address) {
                    continue;
                }

                $kita = new Kita();
                $kita->setName($name)
                    ->setAddress($address)
                    ->setEmail($email)
                    ->setUrl($url)
                    ->setPhone(substr($phone, 0, 20))
                    ->setCreatedAt(new \DateTime());

                $em->persist($kita);
                $em->flush();

                $output->writeln($kita->getName());
            } catch (\Exception $exception) {
                $output->writeln($exception->getMessage());
                continue;
            }
        }
    }
}