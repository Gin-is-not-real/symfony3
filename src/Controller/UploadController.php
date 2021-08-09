<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Service\FileUploader;
use Psr\Log\LoggerInterface;


class UploadController extends AbstractController {
    /**
     * @Route("/upload", name="upload")
     */
    public function toUploadPage(): Response {
        return $this->render('upload/index.html.twig');
    }


    /**
     * @Route("/do-upload", name="do-upload")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */

    //  $uploadDir est defini dans *config/service.yaml
    public function index(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger): Response
    {
        $token = $request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return $this->render('upload/index.html.twig', [
                'message' => "Operation not allowed", 
                'content-type' => 'text/plain'
            ]);  
        }

        $file = $request->files->get('myfile');

        if (empty($file))
        {
            return $this->render('upload/index.html.twig', [
                'message' => "No file specified", 
                'content-type' => 'text/plain'
            ]);   
        }

        $filename = $file->getClientOriginalName();
        $uploader->upload($file, $filename);

        return $this->render('upload/index.html.twig', [
            'message' => "File uploaded", 
            'content-type' => 'text/plain',
            'file' => $uploadDir . "/" . $filename,
            // 'link' => '<a href="' . $uploadDir . "/" . $filename . '">dl</a>'
        ]);
    }
}
