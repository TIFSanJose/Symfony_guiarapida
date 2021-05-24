<?php

namespace App\Controller;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\CommentMessage;
use App\Entity\Conference;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Form\CommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConferenceRepository;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// use App\SpamChecker;

class ConferenceController extends AbstractController
{
    
    private $twig;
    private $entityManager;
    private $bus;

    
    // public function __construct(Environment $twig)
    // public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    #[Route('/')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('homepage', ['_locale' => 'en']);
    }    
    
    // /**
    //  * @Route("/hello/{name}", name="homepage")
    //  */

    // /**
    //  * @Route("/", name="homepage")
    //  */

    // #[Route('/{_locale}/', name: 'homepage')]
    // #[Route('/{_locale<en|fr>}/', name: 'homepage')]
    #[Route('/{_locale<%app.supported_locales%>}/', name: 'homepage')]
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
        // return new Response($this->twig->render('conference/index.html.twig', [ 
        $response = new Response($this->twig->render('conference/index.html.twig', [
        'conferences' => $conferenceRepository->findAll(),
            ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/conference_header", name="conference_header")
     */

    // #[Route('/conference_header', name: 'conference_header')]
    #[Route('/{_locale<%app.supported_locales%>}/conference_header', name: 'conference_header')]
    public function conferenceHeader(ConferenceRepository $conferenceRepository): Response
    {
        // return new Response($this->twig->render('conference/header.html.twig', [
            $response = new Response($this->twig->render('conference/header.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }


    // #[Route('/conference/{id}', name: 'conference')]
    // #[Route('/conference/{slug}', name: 'conference')]
    #[Route('/{_locale<%app.supported_locales%>}/conference/{slug}', name: 'conference')]
    // public function show(Environment $twig, Conference $conference, CommentRepository $commentRepository): Response
    // public function show(Request $request, Environment $twig, Conference $conference, CommentRepository $commentRepository): Response
    // public function show(Request $request, Conference $conference, CommentRepository $commentRepository): Response
    // public function show(Request $request, Conference $conference, CommentRepository $commentRepository, string $photoDir): Response
    // public function show(Request $request, Conference $conference, CommentRepository $commentRepository, SpamChecker $spamChecker, string $photoDir): Response
    // public function show(Request $request, Conference $conference, CommentRepository $commentRepository, string $photoDir): Response
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository, NotifierInterface $notifier, string $photoDir): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        if ($form->isSubmitted()) {
            $notifier->send(new Notification('Can you check your submission? There are some problems with it.', ['browser']));
        }
            

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referer'),
                'permalink' => $request->getUri(),
            ];
            // if (2 === $spamChecker->getSpamScore($comment, $context)) {
            //     throw new \RuntimeException('Blatant spam, go away!');
            // }            

            // $this->entityManager->flush();
            // $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

            $reviewUrl = $this->generateUrl('review_comment', ['id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->bus->dispatch(new CommentMessage($comment->getId(), $reviewUrl, $context));            

            $notifier->send(new Notification('Thank you for the feedback; your comment will be posted after moderation.', ['browser']));

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        // return new Response($twig->render('conference/show.html.twig', [
            return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            // 'comments' => $commentRepository->findBy(['conference' => $conference], ['createdAt' => 'DESC']),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),

            ]));
        }
}
