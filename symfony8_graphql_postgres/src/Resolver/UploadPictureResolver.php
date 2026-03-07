<?php
namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UploadPictureUserPayload;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
// use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Routing\RouterInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use Doctrine\ORM\EntityManagerInterface;


final class UploadPictureResolver implements MutationResolverInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
        private IriConverterInterface $iriConverter,
        private EntityManagerInterface $entityManager,
    ) {}


    /**
     * @param User|null $item
     */
    public function __invoke($item, array $context): UploadPictureUserPayload
    {
        // $output = new ConsoleOutput();

        $input = $context['args']['input'] ?? [];


        $uploadedFile = $input['file'] ?? null;
        if (!$uploadedFile instanceof UploadedFile) {
            throw new \RuntimeException('No file uploaded or invalid file type.');
        }

        $user = $this->iriConverter->getResourceFromIri($input['id']);

        if (!$user instanceof User) {
            throw new \Exception('User not found.');
        }

        $id = (string)$user->getId();
        $fileName = "00" . $id . '.' . $uploadedFile->guessExtension();

        $destination = $this->projectDir . '/public/users';
        $uploadedFile->move($destination, $fileName);    
        $user->setUserpic($fileName);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $user->setMessage('You have updated your profile picture successfully.');
        return new UploadPictureUserPayload(
            $user->getId(),
            $user->getUserpic(),
            $user->getMessage(),
            $user
        );
    }
}
