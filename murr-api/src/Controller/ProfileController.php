<?php


namespace App\Controller;


use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController
{
    /**
     * @Route ("/profile/{$id}", name=profile)
     * @param int $id
     * @param ProfileRepository $ac
     * @return Response
     */
    public function index(int $id, ProfileRepository $ac) : Response
    {
        $account = $ac->GetProfileByResidentID($id);

        return $this->json($account);
    }


}