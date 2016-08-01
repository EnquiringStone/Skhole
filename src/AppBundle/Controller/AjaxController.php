<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Feb-16
 * Time: 22:04
 */

namespace AppBundle\Controller;


use AppBundle\Util\FileHelper;
use AppBundle\Util\SecurityHelper;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AjaxController extends Controller
{
    /**
     * @Route("/{_locale}/secure/ajax/upload/picture", name="app_secure_ajax_upload_picture")
     */
    public function uploadPictureAction(Request $request)
    {
        if(SecurityHelper::hasUserContext($this->get('security.token_storage'))) {
            $data = array();
            foreach($request->files->all() as $file) {
                if($file[0] instanceof UploadedFile) {
                    $file = $file[0];

                    $helper = new FileHelper($file, $this->getUser(), 'pictures');
                    $file->move($helper->getAbsolutePath(), $helper->getName());
                    $data[] = $helper->getRelativePath() . $helper->getName();
                }
            }
            return new Response(json_encode($data), 200, array('Content-Type', 'application/json'));
        }
        throw new AccessDeniedException();
    }

    /**
     * @Route("/{_locale}/secure/generate/student/pdf/{sharedReportId}", name="app_secure_generate_student_pdf")
     */
    public function generateStudentPdfAction($sharedReportId)
    {
        $sharedReport = $this->getDoctrine()->getRepository('AppBundle:Report\SharedReports')->find($sharedReportId);
        if($sharedReport == null || !$sharedReport->getHasAccepted() || $sharedReport->getMentorUserId() != $this->getUser()->getId())
            throw new AccessDeniedException();

        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($sharedReport->getReportId());
        $criteria = array('sharedReport' => $sharedReport, 'pages' => $pages, 'name' => 'front', 'offset' => 0, 'report' => $sharedReport->getReport(), 'viewType' => 'teach');

        $allQuestions = array();
        foreach ($pages as $page)
        {
            $allQuestions[$page['id']] = $this->getDoctrine()->getRepository('AppBundle:Report\AnswerResults')->getAllAnsweredQuestionByCoursePage($sharedReport->getReportId(), $page['id']);
        }
        $criteria['allQuestions'] = $allQuestions;

        $environment = $this->get('twig');
        $html = $environment->render(':pdf:student.report.pdf.html.twig', $criteria);

        $pdf = new \mPDF();
        $pdf->WriteHTML($html);

        $response = new Response($pdf->Output());
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @Route("/{_locale}/generate/report/pdf/{reportId}", name="app_generate_report_pdf")
     */
    public function generateReportPdf($reportId)
    {
        $report = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->find($reportId);

        if($report == null || !$report->getIsComplete()) throw new AccessDeniedException();

        if($this->isGranted('ROLE_USER'))
        {
            if($report->getUserId() != $this->getUser()->getId()) throw new AccessDeniedException();
        }
        else
        {
            if($report->getSessionId() != $this->get('session')->getId()) throw new AccessDeniedException();
        }


        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($report->getId());
        $allQuestions = array();
        foreach ($pages as $page)
        {
            $allQuestions[$page['id']] = $this->getDoctrine()->getRepository('AppBundle:Report\AnswerResults')->getAllAnsweredQuestionByCoursePage($report->getId(), $page['id']);
        }
        $criteria = array('report' => $report, 'pages' => $pages, 'viewType' => 'learn', 'allQuestions' => $allQuestions);

        $html = $this->get('twig')->render(':pdf:student.report.pdf.html.twig', $criteria);

        $pdf = new \mPDF();
        $pdf->WriteHTML($html);

        $response = new Response($pdf->Output());
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}