<?php

namespace App\Test\Controller;

use App\Entity\Medecin;
use App\Repository\MedecinRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MedecinControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MedecinRepository $repository;
    private string $path = '/medecin/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Medecin::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Medecin index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'medecin[email]' => 'Testing',
            'medecin[roles]' => 'Testing',
            'medecin[password]' => 'Testing',
            'medecin[nom]' => 'Testing',
            'medecin[prenom]' => 'Testing',
            'medecin[cin]' => 'Testing',
            'medecin[sexe]' => 'Testing',
            'medecin[telephone]' => 'Testing',
            'medecin[gouvernorat]' => 'Testing',
            'medecin[adresse]' => 'Testing',
            'medecin[confirm_password]' => 'Testing',
            'medecin[titre]' => 'Testing',
            'medecin[adresse_cabinet]' => 'Testing',
            'medecin[fixe]' => 'Testing',
            'medecin[diplome_formation]' => 'Testing',
            'medecin[tarif]' => 'Testing',
            'medecin[cnam]' => 'Testing',
            'medecin[horraire_ouverture]' => 'Testing',
        ]);

        self::assertResponseRedirects('/medecin/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Medecin();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setCin('My Title');
        $fixture->setSexe('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setGouvernorat('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setConfirm_password('My Title');
        $fixture->setTitre('My Title');
        $fixture->setAdresse_cabinet('My Title');
        $fixture->setFixe('My Title');
        $fixture->setDiplome_formation('My Title');
        $fixture->setTarif('My Title');
        $fixture->setCnam('My Title');
        $fixture->setHorraire_ouverture('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Medecin');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Medecin();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setCin('My Title');
        $fixture->setSexe('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setGouvernorat('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setConfirm_password('My Title');
        $fixture->setTitre('My Title');
        $fixture->setAdresse_cabinet('My Title');
        $fixture->setFixe('My Title');
        $fixture->setDiplome_formation('My Title');
        $fixture->setTarif('My Title');
        $fixture->setCnam('My Title');
        $fixture->setHorraire_ouverture('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'medecin[email]' => 'Something New',
            'medecin[roles]' => 'Something New',
            'medecin[password]' => 'Something New',
            'medecin[nom]' => 'Something New',
            'medecin[prenom]' => 'Something New',
            'medecin[cin]' => 'Something New',
            'medecin[sexe]' => 'Something New',
            'medecin[telephone]' => 'Something New',
            'medecin[gouvernorat]' => 'Something New',
            'medecin[adresse]' => 'Something New',
            'medecin[confirm_password]' => 'Something New',
            'medecin[titre]' => 'Something New',
            'medecin[adresse_cabinet]' => 'Something New',
            'medecin[fixe]' => 'Something New',
            'medecin[diplome_formation]' => 'Something New',
            'medecin[tarif]' => 'Something New',
            'medecin[cnam]' => 'Something New',
            'medecin[horraire_ouverture]' => 'Something New',
        ]);

        self::assertResponseRedirects('/medecin/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getCin());
        self::assertSame('Something New', $fixture[0]->getSexe());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getGouvernorat());
        self::assertSame('Something New', $fixture[0]->getAdresse());
        self::assertSame('Something New', $fixture[0]->getConfirm_password());
        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getAdresse_cabinet());
        self::assertSame('Something New', $fixture[0]->getFixe());
        self::assertSame('Something New', $fixture[0]->getDiplome_formation());
        self::assertSame('Something New', $fixture[0]->getTarif());
        self::assertSame('Something New', $fixture[0]->getCnam());
        self::assertSame('Something New', $fixture[0]->getHorraire_ouverture());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Medecin();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setCin('My Title');
        $fixture->setSexe('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setGouvernorat('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setConfirm_password('My Title');
        $fixture->setTitre('My Title');
        $fixture->setAdresse_cabinet('My Title');
        $fixture->setFixe('My Title');
        $fixture->setDiplome_formation('My Title');
        $fixture->setTarif('My Title');
        $fixture->setCnam('My Title');
        $fixture->setHorraire_ouverture('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/medecin/');
    }
}
