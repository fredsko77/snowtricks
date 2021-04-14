<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Group;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
use App\Helpers\Helpers;
use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * @var Helpers $helpers
     */
    private $helpers;

    /**
     * @var GroupRepository $groupRepository
     */
    private $groupRepository;

    /**
     * @var TrickRepository $trickRepository
     */
    private $trickRepository;

    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    private const ROLES = ['ROLE_USER', 'ROLE_ADMIN'];

    private const GROUPS = [
        ['name' => 'Les grabs', 'description' => 'Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. ».'],
        ['name' => 'Les rotations', 'description' => 'On désigne par le mot « rotation » uniquement des rotations horizontales.  Le principe est d\'effectuer une rotation horizontale pendant le saut, puis d\'atterrir en position switch ou normal.'],
        ['name' => 'Les flips', 'description' => 'Un flip est une rotation verticale.'],
    ];

    private const COMMENTS = [
        "Génial !",
        "Super !",
        "Incroyable !",
        "Woooooooowwww !",
        "Lorem ipsum ",
        "Lorem ipsum dolor sit amet.",
        "Lorem ipsum dolor sit.",
        "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ex, voluptatibus.",
        "Lorem ipsum dolor sit amet consectetur adipisicing elit.",
        "Lorem ipsum dolor sit amet consectetur.",
    ];

    private const TRICKS = [
        ['name' => 'Mute', 'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.'],
        ['name' => 'Sad', 'description' => 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.'],
        ['name' => 'Indy', 'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.'],
        ['name' => 'Stalefish', 'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.'],
        ['name' => 'Tail grab', 'description' => 'Saisie de la partie arrière de la planche, avec la main arrière.'],
        ['name' => 'Nose grab', 'description' => 'Saisie de la partie avant de la planche, avec la main avant.'],
        ['name' => 'Japan air', 'description' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside'],
        ['name' => 'Seat belt', 'description' => 'Saisie du carre frontside à l\'arrière avec la main avant.'],
        ['name' => 'Truck driver', 'description' => 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).'],
        ['name' => '360', 'description' => 'Trois six pour un tour complet'],
        ['name' => '720', 'description' => 'Sept deux pour deux tours complets ;'],
        ['name' => 'Mc Twist', 'description' => 'Un grand classique des rotations tête en bas qui se fait en backside, sur un mur backside de pipe. Le Mc Twist est généralement fait en japan, un grab très tweaké (action d\'accentuer un grab en se contorsionnant).'],
        ['name' => 'Cork', 'description' => 'Le diminutif de corkscrew qui signifie littéralement tire-bouchon et désignait les premières simples rotations têtes en bas en frontside. Désormais, on utilise le mot cork à toute les sauces pour qualifier les figures où le rider passe la tête en bas, peu importe le sens de rotation. Et dorénavant en compétition, on parle souvent de double cork, triple cork et certains riders vont jusqu\'au quadruple cork !'],
        ['name' => '270', 'description' => 'Désigne le degré de rotation, soit 3/4 de tour, fait en entrée ou en sortie sur un jib. Certains riders font également des rotations en 450 degrés avant ou après les jibs.'],
        ['name' => 'Crippler', 'description' => 'Une autre rotation tête en bas classique qui s\'apparente à un backflip sur un mur frontside de pipe ou un quarter.'],
    ];

    private const VIDEOS = [
        'https://www.youtube.com/embed/SQyTWk7OxSI',
        'https://www.youtube.com/embed/V9xuy-rVj9w',
        'https://www.youtube.com/embed/GS9MMT_bNn8',
        'https://www.youtube.com/embed/CA5bURVJ5zk',
        'https://www.youtube.com/embed/UNItNopAeDU',
        'https://www.youtube.com/embed/CzDjM7h_Fwo',
        'https://www.youtube.com/embed/hPLZPJ_Sw_0',
        'https://www.youtube.com/embed/6gFsbU3GWF0',
        'https://www.youtube.com/embed/_2TkKJ6euDc',
    ];

    private const IMAGES = [
        'bradley-dunn-9SGGun3iIig-unsplash.jpg',
        'fabian-schneider-LXswmpRHORY-unsplash.jpg',
        'fabian-schneider-LXswmpRHORY-unsplash.jpg',
        'image-snowboard-04t85g46.jpg',
        'image-snowboard-04t85g46.jpg',
        'mattias-olsson-nQz49efZEFs-unsplash.jpg',
        'mute-air-on-snowboard-1438217-1919x1368.jpg',
        'pexels-evgenia-kirpichnikova-1973293.jpg',
        'pexels-john-robertnicoud-38242.jpg',
        'pexels-pixabay-209817.jpg',
        'pexels-visit-almaty-848599.jpg',
        'snowboard-227541_1920.jpg',
        'snowboarding-1527861-638x478.jpg',
        'visit-almaty-wN4D-mVR7fE-unsplash.jpg',
    ];

    public function __construct(
        Helpers $helpers,
        UserPasswordEncoderInterface $encoder,
        GroupRepository $groupRepository,
        TrickRepository $trickRepository,
        UserRepository $userRepository
    ) {
        $this->helpers = $helpers;
        $this->encoder = $encoder;
        $this->groupRepository = $groupRepository;
        $this->trickRepository = $trickRepository;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {

        $slugify = new Slugify();

        // Initialisation des users
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $data = [
                'pseudo' => "admin0{$i}",
                'email' => "admin-0{$i}@gmail.com",
                'roles' => [self::ROLES[random_int(0, 1)]],
                'token' => $this->helpers->generateToken(80),
                'created_at' => $this->helpers->now(),
                'image' => '',
                'confirm' => null,
            ];
            $user->_hydrate($data)
                ->setPassword($this->encoder->encodePassword($user, "1234P@sse"))
            ;

            $manager->persist($user);
            $manager->flush();
        }

        // Initialisation des groups
        for ($i = 0; $i < count(self::GROUPS); $i++) {
            $group = new Group();
            $group->_hydrate(self::GROUPS[$i])
                ->setSlug($slugify->slugify($group->getName()))
            ;

            $manager->persist($group);
            $manager->flush();
        }

        // Initialisation des tricks
        for ($i = 0; $i < count(self::TRICKS); $i++) {
            $trick = new Trick();
            $trick->_hydrate(self::TRICKS[$i])
                ->setCreatedAt($this->helpers->now())
                ->setUser($this->userRepository->find(random_int(1, 9)))
                ->setSlug($slugify->slugify($trick->getName()))
                ->setPoster('')
                ->setGroup($this->groupRepository->find(random_int(1, count(self::GROUPS))))
            ;

            $manager->persist($trick);
            $manager->flush();
        }

        // Initialisation des comments
        for ($i = 0; $i < 150; $i++) {
            $comment = new Comments();
            $comment->setComment(self::COMMENTS[random_int(0, (count(self::COMMENTS) - 1))])
                ->setCreatedAt($this->helpers->now())
                ->setTrick($this->trickRepository->find(random_int(1, count(self::TRICKS))))
                ->setUser($this->userRepository->find(random_int(1, 9)))
            ;

            $manager->persist($comment);
            $manager->flush();
        }

        // Initialisation des videos
        for ($i = 0; $i <= 45; $i++) {
            $video = new Videos();
            $video->setTrick($this->trickRepository->find(random_int(1, count(self::TRICKS))))
                ->setCreatedAt($this->helpers->now())
                ->setUrl(self::VIDEOS[random_int(0, (count(self::VIDEOS) - 1))])
            ;

            $manager->persist($video);
            $manager->flush();
        }

        // Initialisation des images
        for ($i = 1; $i <= 50; $i++) {
            $image = new Image();
            $image->setType('trick_image')
                ->setCreatedAt($this->helpers->now())
                ->setPath('/fixtures/' . self::IMAGES[random_int(0, (count(self::IMAGES)) - 1)])
                ->setTrick($this->trickRepository->find(random_int(1, 30)))
            ;

            $manager->persist($image);
            $manager->flush();
        }

    }
}
