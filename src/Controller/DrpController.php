<?php

namespace App\Controller;

use App\Entity\Contrats;
use App\Entity\Pigistes;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/drp", name="drp_")
 */
class DrpController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $firstContract = $this->getDoctrine()->getRepository(Contrats::class)->findFirstContract();
        if (!isset($firstContract)) {
            return $this->render('errors/noContract.html.twig', [
                'active_menu' => ''
            ]);
        }
        $start = $firstContract->getDateDebut()->modify('first day of this month');
        $end = (new \DateTime())->modify('last day of this month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);
        $months = array();
        foreach ($period as $dt) {
            $months[] = $dt;
        }
        $months = array_reverse($months);
        return $this->render('drp/index.html.twig', [
            'months' => $months,
            'active_menu' => 'drp'
        ]);
    }

    /**
     * @Route("/generate/{year}/{month}", name="generate")
     */
    public function generate($year, $month): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $contracts = $this->getDoctrine()->getRepository(Contrats::class)->getContractByMonth($year, $month);
        $pigistes = array();
        foreach ($contracts as $contrat) {
            if (!in_array($contrat->getPigiste(), $pigistes, true)) {
                $pigistes[] = $contrat->getPigiste();
            }
        }
        $date_debut = new \DateTime("$year-$month-01T00:00:00");
        $date_fin = (clone $date_debut)->modify('last day of this month');
        $inputFileName = 'xlsx/drp.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $spreadsheet->getSheet(0)->setCellValue('A2', 'DEMANDE DE REGLEMENT DE PIGES pour la période du ' . $date_debut->format('d/m/Y') . ' au ' . $date_fin->format('d/m/Y'));
        $i = 1;
        foreach ($pigistes as $pigiste) {
            $y = 8;
            $contrats = $this->getDoctrine()->getRepository(Contrats::class)->getDrpContract($pigiste, $year, $month);
            $clonedWorkSheet = clone $spreadsheet->getSheet(0);
            $clonedWorkSheet->setTitle($pigiste->getPrenom() . ' ' . strtoupper($pigiste->getNom()));
            $spreadsheet->addSheet($clonedWorkSheet);
            $sheet = $spreadsheet->getSheet($i);
            $sheet->setCellValue('B5', strtoupper($pigiste->getNom()));
            $sheet->setCellValue('E5', $pigiste->getPrenom());
            $sheet->setCellValue('L5', $pigiste->getMatricule());
            if (count($contrats) > 1) {
                $sheet->setCellValue('B6', count($contrats) . ' CONTRATS');
            } else {
                $sheet->setCellValue('B6', count($contrats) . ' CONTRAT');
            }
            foreach ($contrats as $contrat) {
                $tarif = $contrat->getTarif();
                if ($tarif->getName() == 'Information - Alerte téléphonique') {
                    $col = 'C';
                } elseif ($tarif->getName() == 'Document') {
                    $col = 'D';
                } elseif ($tarif->getName() == 'Couverture de match - Professionnel') {
                    $col = 'E';
                } elseif ($tarif->getName() == 'Couverture de match - Stagiaire de + de 1 an') {
                    $col = 'F';
                } elseif ($tarif->getName() == 'Couverture de match - Stagiaire 0 à 1 an') {
                    $col = 'G';
                } elseif ($tarif->getName() == 'Journée - Professionnel') {
                    $col = "H";
                } elseif ($tarif->getName() == "Journée - Stagiaire de + de 1 an") {
                    $col = 'I';
                } elseif ($tarif->getName() == 'Journée - Stagiaire 0 à 1 an') {
                    $col = 'J';
                } elseif ($tarif->getName() == 'Demi-journée - Professionnel') {
                    $col = 'K';
                } elseif ($tarif->getName() == 'Demi-journée - Stagiaire de + de 1 an') {
                    $col = 'L';
                } elseif ($tarif->getName() == 'Demi-journée - Stagiaire 0 à 1 an') {
                    $col = 'M';
                }
                if ($contrat->getNbJours() == 1) {
                    $y += 1;
                    $sheet->setCellValue('A' . $y, $contrat->getDateDebut()->format('d/m/Y'));
                    $sheet->setCellValue('B' . $y, $contrat->getMotif());
                    $sheet->setCellValue($col . $y, $tarif->getMontant());
                    $sheet->setCellValue('N' . $y, '1');
                    $sheet->setCellValue('O' . $y, '2');
                    $y += 1;
                    $sheet->setCellValue($col . $y, $tarif->getMontant());
                    $sheet->getStyle($col . $y)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getFill()->setFillType('solid')->getStartColor()->setARGB(Color::COLOR_YELLOW);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->setCellValue('N' . $y, '1');
                    $sheet->setCellValue('O' . $y, '2');
                    $sheet->setCellValue('P' . $y, $contrat->getCentreDeCout());
                    $sheet->setCellValue('Q' . $y, 'EM000854');
                } elseif ($contrat->getNbJours() > 1) {
                    $y += 1;
                    $nbJours = $contrat->getNbJours();
                    $z = 1;
                    while ($z <= $nbJours) {
                        $dateDebut = $contrat->getDateDebut();
                        $sheet->setCellValue('A' . $y, $dateDebut->format('d/m/Y'));
                        $sheet->setCellValue('B' . $y, $contrat->getMotif());
                        $sheet->setCellValue($col . $y, $tarif->getMontant());
                        $sheet->setCellValue('N' . $y, '1');
                        $sheet->setCellValue('O' . $y, '2');
                        $dateDebut->modify('+ 1 day');
                        $y += 1;
                        $z += 1;
                    }
                    $sheet->setCellValue($col . $y, ($tarif->getMontant()) * $nbJours);
                    $sheet->getStyle($col . $y)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getFill()->setFillType('solid')->getStartColor()->setARGB(Color::COLOR_YELLOW);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $y . ':Q' . $y)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->setCellValue('N' . $y, $nbJours);
                    $sheet->setCellValue('O' . $y, $nbJours * 2);
                    $sheet->setCellValue('P' . $y, $contrat->getCentreDeCout());
                    $sheet->setCellValue('Q' . $y, 'EM000854');

                }
                $sheet->getStyle('A9:Q' . $y)->getFont()->setSize(11);
            }
            $sheet->getStyle('C6')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $y += 2;
            $sheet->setCellValue('N' . $y, 'Le directeur');
            $i += 1;
        }
        $spreadsheet->removeSheetByIndex(0);
        $writer = new Xlsx($spreadsheet);
        $filename = 'drp' . $date_debut->format('mY') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return $this->file($temp_file, $filename);
    }
}
