<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UpdateProfileUserPayload;
use App\Dto\ActivateMfaPayload;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface ;
use OTPHP\TOTP;
use OTPHP\TOTPInterface; 

final class ActivateMfaResolver implements MutationResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TotpAuthenticatorInterface $totpAuthenticator,
        private BuilderInterface $qrCodeBuilder
    ) {}

    /**
     * @param User|null $item The deserialized user from the input
     */
 
public function __invoke($item, array $context): ActivateMfaPayload
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $input = $context['args']['input'];
    if (isset($input['twofactorenabled']) && $input['twofactorenabled']) {
                    $item->setMessage('Multi-Factor has been enabled successfully.');
                    $secret = $this->totpAuthenticator->generateSecret();

                    //VALID FORMAT: otpauth://totp/ISSUER:ACCOUNTNAME?secret=SECRETKEY&issuer=ISSUER
                    $issuer = 'SUPERCAR INC.';
                    $accountName = $item->getEmail();
                    $secret = $secret; // Base32 encoded secret key
            
                    // Encode components for the VALID URI
                    $encodedIssuer = urlencode($issuer);
                    $encodedAccountName = urlencode($accountName);
            
                    // Construct the otpauth URI, DONT CHANGE THE otpauth://totp/%s:%s?secret=%s&issuer=%s
                    $uri = sprintf(
                        'otpauth://totp/%s:%s?secret=%s&issuer=%s',
                        $encodedIssuer,
                        $encodedAccountName,
                        $secret,
                        $encodedIssuer
                    );

                    $result = $this->qrCodeBuilder->build(
                        data: $uri,
                        size: 200,
                        margin: 10
                    );
                    
                    // Get the Base64 data URI
                    $dataUri = $result->getDataUri();     
                    $item->setSecretkey($secret);
                    $item->setQrcodeurl($dataUri);

    } else {
        $item->setMessage('Multi-Factor has been disabled successfully.');
        $item->setSecretkey(null);
        $item->setQrcodeurl(null);
    }
        
    $this->entityManager->persist($item);
    $this->entityManager->flush();

    return new ActivateMfaPayload(
        (string) $item->getId(),
        $item->getQrcodeurl(),
        $item->getMessage(),
        $item
    );    
 }
}


// ===========REQUEST=================
// mutation activateMfa($id: ID!, $twofactorenabled: Boolean!) {
// 	activateMfaUser(input: {id : $id, twofactorenabled: $twofactorenabled})  {
//     user {
//       id
//       qrcodeurl
//       message
//     }
//   }
// }

// ===========VARIABLES===========
// {
//   "id": "1",
//   "twofactorenabled": false
// }
