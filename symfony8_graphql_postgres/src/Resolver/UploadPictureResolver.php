<?php
namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UploadPicturePayload;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Console\Output\ConsoleOutput;

final class UploadPictureResolver implements MutationResolverInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir
    ) {}

    /**
     * @param User|null $item
     */
    public function __invoke($item, array $context): UploadPicturePayload
    {
        $input = $context['args']['input'] ?? [];

        $output = new ConsoleOutput();
        $output->writeln('testing........................................');


        $uploadedFile = $input['file'] ?? null;
        if (!$uploadedFile instanceof UploadedFile) {
            throw new \RuntimeException('No file uploaded or invalid file type.');
        }

        $id = $input['id'];  
        $fileName = "00" . $basename($id) . '.' . $uploadedFile->guessExtension();
        
        // $fileName = "00" . str_replace('/api/users/', '', $args['id']) . '.' . $uploadedFile->guessExtension();
        // $destination = $this->projectDir . '/public/users';
    
        $destination = $this->projectDir . '/public/users';
        $uploadedFile->move($destination, $fileName);    
        
        $item->setUserpic($fileName);
        $item->setMessage('You have updated your profile picture successfully.');

        return new UploadPicturePayload(
            (string) $item->getId(),
            $item->getUserpic(),
            $item->getMessage(),
            $item
        );    
    }
}
