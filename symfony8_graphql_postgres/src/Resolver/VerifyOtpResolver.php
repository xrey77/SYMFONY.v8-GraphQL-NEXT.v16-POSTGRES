<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\VerifyOtpPayload;
use OTPHP\TOTP;
use OTPHP\TOTPInterface; 

final class VerifyOtpResolver implements MutationResolverInterface
{
    public function __construct(
    ) {}

    /**
     * @param User|null $item The deserialized user from the input
     */
 
public function __invoke($item, array $context): VerifyOtpPayload
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $input = $context['args']['input'];
    if (!$input['otp']) {
        throw new \RuntimeException('OTP code is required.');
    } 
    $otp = $input["otp"];
    $totp = TOTP::create($item->getSecretkey());
    if ($totp->verify($otp)) {
        $item->setMessage('Multi-Factor has been verified successfully.');
    } else {
            throw new \RuntimeException('Invalid OTP code, please try again.');
    }
    
    return new VerifyOtpPayload(
        (string) $item->getId(),
        $item->getUsername(),
        $item->getMessage(),
        $item
    );    
 }
}


// ===========REQUEST=================
// mutation verifyOtp($id: ID!, $otp: String!) {
//   verifyOtpUser(input: {id: $id, otp: $otp})  {
//     user {
//       id
//       username
//       message
//     }
//   }
// }

// ===========VARIABLES===========
// {
//   "id": "1",
//   "otp": "232343"
// }
