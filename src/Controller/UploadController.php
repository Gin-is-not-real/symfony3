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
     * @Route(/"download/{filename}", name="download")
     */
    // public function download(string $uploadDir, $filename) {
    //     $request = $this->get('request');
    //     $path = $this->get('kernel')->getRootDir() . $uploadDir . "/";
    // }

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

            // return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
            //     ['content-type' => 'text/plain']);
            return $this->render('upload/index.html.twig', [
                'message' => "Operation not allowed", 
                'content-type' => 'text/plain'
            ]);  
        }

        $file = $request->files->get('myfile');

        if (empty($file))
        {
            // return new Response("No file specified",
            //    Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);

            return $this->render('upload/index.html.twig', [
                'message' => "No file specified", 
                'content-type' => 'text/plain'
            ]);   
        }

        $filename = $file->getClientOriginalName();
        $uploader->upload($uploadDir, $file, $filename);

        // return new Response("File uploaded",  Response::HTTP_OK,
        //     ['content-type' => 'text/plain']);

        return $this->render('upload/index.html.twig', [
            'message' => "File uploaded", 
            'content-type' => 'text/plain',
            'filename' => $filename,
            'uploaddir' => $uploadDir,
            'file' => $uploadDir . "/" . $filename
        ]);
    }
}
