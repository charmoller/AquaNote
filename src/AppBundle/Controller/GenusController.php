<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Repository\GenusNoteRepository;
use AppBundle\Service\MarkdownTransformer;
use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction(): Response
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));

        $genusNote = new GenusNote();
        $genusNote->setUsername('AquaWeaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('I counted 8 legs... as they wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->persist($genusNote);
        $em->flush();

        return new Response('<html><body>Genus created</body></html>');
    }

    /**
     * @Route("/genus")
     */
    public function listAction(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository(Genus::class)
            ->findAllPublishedOrderedByRecentlyActive();

        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName, MarkdownTransformer $markdownTransformer): Response
    {
        $em =$this->getDoctrine()->getManager();
        $genus = $em->getRepository(Genus::class)
            ->findOneBy(['name' => $genusName]);

        if (!$genus) {
            throw $this->createNotFoundException('No genus found');
        }
        //$markdownParser = new MarkdownTransformer();
        $funFact = $markdownTransformer->parse($genus->getFunFact());

        // todo - add the caching back later
        /*
        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
        $key = md5($funFact);
        if ($cache->contains($key)) {
            $funFact = $cache->fetch($key);
        } else {
            sleep(1); // fake how slow this could be
            $funFact = $this->get('markdown.parser')
                ->transform($funFact);
            $cache->save($key, $funFact);
        } */

        $this->get('logger')
            ->info('Showing genus: '.$genusName);

       $recentNotes = $em->getRepository(GenusNote::class)
           ->findAllRecentNotesForGenus($genus);

       return $this->render('genus/show.html.twig', [
           'genus' => $genus,
           'funFact' => $funFact,
           'recentNoteCount' => count($recentNotes),
        ]);
    }

    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus): Response
    {
        dump($genus);
        $notes = [];

        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }
        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
