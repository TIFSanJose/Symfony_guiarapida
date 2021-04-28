<?php

namespace App\Controller;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConferenceRepository;
use Twig\Environment;


class ConferenceController extends AbstractController
{
    
    private $twig;
    
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * @Route("/hello/{name}", name="homepage")
     */
    
    // public function index(): Response
    // public function index(Request $request): Response
    // public function index(string $name = ''): Response
    // public function index(Environment $twig, ConferenceRepository $conferenceRepository): Response
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        // return $this->render('conference/index.html.twig', [
        //     'controller_name' => 'ConferenceController',
        // ]);
        // $greet = '';
        // if ($name = $request->query->get('hello')) {
        // if ($name ) {
        //     $greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
        // }
        //         return new Response(<<<EOF
        //             <html>
        //                 <body>
        //                     $greet
        //                     <img src="/images/under-construction.gif" />
        //                 </body>
        //             </html>
        //             EOF
        //         );

        // return new Response($twig->render('conference/index.html.twig', [
            return new Response($this->twig->render('conference/index.html.twig', [ 
            'conferences' => $conferenceRepository->findAll(),
                ]));

    }

    // #[Route('/conference/{id}', name: 'conference')]
    #[Route('/conference/{slug}', name: 'conference')]
    // public function show(Environment $twig, Conference $conference, CommentRepository $commentRepository): Response
    // public function show(Request $request, Environment $twig, Conference $conference, CommentRepository $commentRepository): Response
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);


        // return new Response($twig->render('conference/show.html.twig', [
            return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            // 'comments' => $commentRepository->findBy(['conference' => $conference], ['createdAt' => 'DESC']),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            

            ]));
        }
}
