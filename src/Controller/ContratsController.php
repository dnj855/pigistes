<?php

namespace App\Controller;

use App\Entity\Contrats;
use App\Form\ContratsType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contrats", name="contrats_")
 */
class ContratsController extends AbstractController
{
    /**
     * @Route("/list/{year}/{month}", name="index")
     */
    public function index($year = null, $month = null, Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!(bool)$year && !(bool)$month) {
            $year = date('Y');
            $month = date('m');
        }
        $date = new \DateTime("$year-$month-01T00:00:00");
        $data = $this->getDoctrine()->getRepository(Contrats::class)->getContractByMonth($year, $month);
        $contrats = $paginator->paginate($data, $request->query->getInt('page', 1), 10);
        $firstContract = $this->getDoctrine()->getRepository(Contrats::class)
            ->findFirstContract();
        $lastContract = $this->getDoctrine()->getRepository(Contrats::class)
            ->findLastContract();
        if (!isset($firstContract)) {
            $dateFirstContract = $date->format('m/Y');
        } else {
            $dateFirstContract = $firstContract->getDateDebut()->format('m/Y');
        }
        if (!isset($lastContract)) {
            $dateLastContract = $date->format('m/Y');
        } else {
            $dateLastContract = $lastContract->getDateDebut()->format('m/Y');
        }
        if ($dateFirstContract == $date->format('m/Y')) {
            $beforeMonth = false;
        } else {
            $beforeMonth = true;
        }
        if ($dateLastContract == $date->format('m/Y')) {
            $afterMonth = false;
        } else {
            $afterMonth = true;
        }
        return $this->render('contrats/index.html.twig', [
            'active_menu' => 'contrats',
            'contrats' => $contrats,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'after_month' => $afterMonth,
            'before_month' => $beforeMonth
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->getDoctrine()->getRepository(Contrats::class)->getContractByYear(date('Y')) > 0) {
            $contractNumber = $this->getDoctrine()->getRepository(Contrats::class)->findLastContract()->getNumero();
            $contractNumber += 1;
        } else {
            $number = sprintf('%04d', 1);
            $contractNumber = date('Y') . $number;
        }
        $contrat = new Contrats($contractNumber);
        $form = $this->createForm(ContratsType::class, $contrat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contrat = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contrat);
            $entityManager->flush();
            $this->addFlash('info', 'Le contrat n°' . $contrat->getNumero() . ' a bien été enregistré.');
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->render('contrats/add.html.twig', [
            'form' => $form->createView(),
            'active_menu' => 'contrats'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Contrats $contrat, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(ContratsType::class, $contrat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contrat = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contrat);
            $entityManager->flush();
            $this->addFlash('info', 'Le contrat n°' . $contrat->getNumero() . ' a bien été modifié.');
            return $this->redirectToRoute('contrats_index');
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->render('contrats/edit.html.twig', [
            'form' => $form->createView(),
            'active_menu' => 'contrats'
        ]);
    }

    /**
     * @Route("/disable/{id}", name="disable")
     */
    public function disable(Contrats $contrats, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $contrats->setActive(false);
        $this->getDoctrine()->getManager()->persist($contrats);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/enable/{id}", name="enable")
     */
    public function enable(Contrats $contrats, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $contrats->setActive(true);
        $this->getDoctrine()->getManager()->persist($contrats);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/print/{id}", name="print")
     */
    public function print(Contrats $contrats)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
        $pdf->SetCreator('Radio France');
        $pdf->SetAuthor('Radio France');
        $pdf->SetTitle('Contrat n°' . $contrats->getNumero());
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(2, 5, 2);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
        $pdf->setCellMargins(1, 0, 1, 0);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        $txt = "CONTRAT DE TRAVAIL " . $contrats->getNumero();
        $pdf->SetFont('times', 'B', 12, '', true);
        $pdf->MultiCell(0, 0, $txt, 1, "C", false, 1, 28, 3);
        $pdf->Image("img/logo.jpg", 3, 3, 25, 25, '', '', '', true);
        $pdf->ln(15);
        $y = $pdf->getY();
        $pdf->setFont('times', '', 12);
        $pdf->MultiCell(103, 8, 'EMPLOYEUR', 1, "C", false, 0, 0, $y + 3, $valign = "M");
        $pdf->MultiCell(103, 8, 'SALARIE', 1, "C", false, 1, 103, $y + 3, $valign = "M");
        $pdf->setFont('times', '', 11);
        $html = "
<table style='border:none'>
<tr>
<td><strong>Matricule Radio France :</strong></td>
<td>" . $contrats->getPigiste()->getMatricule() . "</td>
</tr>
<tr>
<td><strong>Nom :</strong></td>
<td>" . strtoupper($contrats->getPigiste()->getNom()) . "</td>
</tr>
<tr>
<td><strong>Prénom :</strong></td>
<td>" . $contrats->getPigiste()->getPrenom() . "</td>
</tr>
<tr>
<td><strong>Pseudonyme :</strong></td>
<td></td>
</tr>
<tr>
<td><strong>Adresse :</strong></td>
<td>" . $contrats->getPigiste()->getAdresse() . "<br/>
" . $contrats->getPigiste()->getCodePostal() . " " . $contrats->getPigiste()->getVille() . "
</td>
</tr>
<tr><td></td></tr>
<tr>
<td><strong>N° S.S. :</strong></td>
<td>" . $contrats->getPigiste()->getSecuriteSociale() . "</td>
</tr>
<tr>
<td><strong>Date et lieu de naissance :</strong></td>
</tr>
<tr>
<td>" . $contrats->getPigiste()->getDateDeNaissance()->format('d/m/Y') . "</td>
<td>" . $contrats->getPigiste()->getLieuDeNaissace() . "</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><strong>Nationalité :</strong></td>
<td>" . $contrats->getPigiste()->getNationalite() . "</td>
</tr>
</table>
<table style='border:none'>
<tr>
<td><strong>Titre de séjour :</strong></td>
</tr>
<tr>
<td><strong>N° :</strong></td>
</tr>
</table>
";
        $pdf->WriteHTMLCell(103, 0, 103, $y + 11, $html, 1, 1, false);
        $html = "
<table style='border: none'>
    <tr>
        <td><strong>Dénomination sociale : </strong></td>
        <td>RADIO FRANCE</td>
    </tr>
    <tr>
        <td><strong>Adresse :</strong></td>
        <td>116 Av. du Pdt KENNEDY<br/>
            75220 PARIS CEDEX 16
        </td>
    </tr>
    <tr>
        <td><strong>Code NAF :</strong></td>
        <td>6010Z</td>
    </tr>
    <tr>
        <td colspan='2' align='center'><strong>Service d'affectation :</strong></td>
    </tr>
    <tr>
        <td><strong>Adresse :</strong></td>
        <td>France Bleu Normandie -Rouen-<br/>
        Hangar A, Quai de Boisguilbert<br/>
        76000 ROUEN</td>
    </tr>
</table>
<table style='border:none'>
    <tr>
        <td><strong>N° Siret :</strong> 326094471 01494-01155-01270</td>
    </tr>
    <tr style='border-bottom-style: dashed; border-bottom-width: 1px'>
        <td><strong>URSSAF de la Hte-Garonne : 000020326094471</strong></td>
    </tr>
</table>
<table style='border:none'>
    <tr>
        <td><strong>Direction :</strong></td>
        <td>Délégation Régionale Nord-Normandie</td>
    </tr>
    <tr>
        <td><strong>Service :</strong></td>
        <td>France Bleu Normandie -Rouen-</td>
    </tr>
    <tr>
        <td><strong>Centre de coût :</strong></td>
        <td><em><strong>" . $contrats->getCentreDeCout() . "</strong></em></td>
    </tr>
</table>
";
        $pdf->WriteHTMLCell(103, 0, 0, $y + 11, $html, 1, 1, false);
        $pdf->setFont('times', 'I', 9);
        $txt = "Le présent contrat à durée déterminée d’usage constant régi par les articles L.1242-2-3° et D.1242-1 du code du travail, vise la prestation ci-dessus.";
        $pdf->Cell('', '', $txt, 0, 1, 'L', false);
        $pdf->setFont('times', 'B', 11);
        $pdf->Cell('27', '10', 'Code emploi :', 0, 0, 'L', false, $valign = 'C');
        $pdf->setFont('times', 'BI', 14);
        $pdf->Cell('70', '10', $contrats->getPigiste()->getCodeEmploi(), 0, 0, 'C');
        $pdf->setFont('times', 'B', 11);
        $pdf->Cell('27', '10', 'Libellé :', 0, 0, 'L', false, $valign = 'C');
        $pdf->setFont('times', 'BI', 14);
        $pdf->Cell('70', 10, $contrats->getPigiste()->getLibelle(), 0, 1, 'C');
        $pdf->setFont('times', 'B', 11);
        $pdf->Cell('', '', 'Convention Collective Nationale de Travail des Journalistes - Avenant audiovisuel', 0, 1, 'L', false);
        $y = $pdf->getY();

        $html = "<table style='border:none'>
<tr>
<td><strong>Durée du contrat :</strong></td>
<td></td>
</tr>
<tr>
<td><em>Date de début :</em></td>
<td><em><strong>" . $contrats->getDateDebut()->format('d/m/Y') . "</strong></em></td>
</tr>
<tr>
<td><em>Date de fin :</em></td>
<td><em><strong>" . $contrats->getDateFin()->format('d/m/Y') . "</strong></em></td>
</tr>
</table>";
        $pdf->setFont('times', '', 11);
        $pdf->WriteHTMLCell('103', '17', '0', $y + 1, $html, 1);
        $html = "
        <table>
        <tr>
        <td><strong>Durée du travail ;</strong></td>
        <td></td>
</tr>
<tr>
<td><em>Nbre de jours SS :</em></td>
<td><em><strong>" . $contrats->getNbJours() . "</strong></em></td>
</tr>
</table>
        ";
        $pdf->WriteHTMLCell('103', '17', '103', $y + 1, $html, 1, 0);
        $html = "
        <table>
        <tr>
        <td><strong>Rémunération brute :</strong></td>
        <td></td>
        <td><strong>Périodicité du versement :</strong></td>
<td>Au mois</td>
</tr>
<tr>
<td><strong>Montant pige :</strong></td>
<td><em><strong>" . str_replace('.', ',', $contrats->getTarif()->getMontant()) . "</strong></em></td>
<td><strong>Modalités de versement :</strong></td>
<td>Virement</td>
</tr>
        </table>
        ";
        $pdf->WriteHTMLCell('', '12', 0, $y + 18, $html, 1, 1);
        $pdf->WriteHTML('<strong>Régime congés :</strong> 1/10ème à chaque versement de salaire', 1, false);
        $html = "<table>
<tr>
<td>
<strong>Caisse de retraite :</strong><br/>
ANEP PIGISTES / IRPS<br/>
Groupe AUDIENS / 74 rue Jean Bleuzen<br/>
92170 Vanves
</td>
<td>
<strong>Organisme de prévoyance :</strong><br/>
Bellini Prévoyance - Pigistes<br/>
Groupe AUDIENS / 74 rue Jean Bleuzen<br/>
92170 Vanves
</td>
</tr>
</table>";
        $pdf->WriteHTMLCell('', '', 0, '', $html, 1, 1);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell('103', '7', "Nature de la prestation / Motif :", 0, 0, 'L', false);
        $pdf->SetFont('times', 'BI', 14);
        $pdf->Cell('103', '7', $contrats->getMotif(), 0, 1, 'L', false);
        $pdf->setFont('times', 'B', 11);
        $pdf->Cell('27', '10', 'Jour(s) de travail :', 0, 0, 'L', false, $valign = 'C');
        $pdf->setFont('times', 'BI', 14);
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $dateDebut = strtotime($contrats->getDateDebut()->format('Y-m-d'));
        $pdf->Cell('70', '10', strftime('%A %d %B %Y', $dateDebut), 0, 0, 'C');
        $pdf->setFont('times', 'B', 11);
        $pdf->Cell('27', '10', 'Au :', 0, 0, 'L', false, $valign = 'C');
        $pdf->setFont('times', 'BI', 14);
        $dateFin = strtotime($contrats->getDateFin()->format('Y-m-d'));
        $pdf->Cell('70', 10, strftime('%A %d %B %Y', $dateFin), 0, 1, 'C');
        $pdf->setFont('times', 'I', 9);
        $txt = 'Vous êtes prié de retourner au représentant de Radio France, par tout moyen à votre disposition (remise en main propre, fax, courrier),  au plus tard le jour de la prestation,  les deux exemplaires dûment paraphés, datés et signés ci-dessous. A défaut de signature, le présent contrat sera considéré comme caduc.';
        $pdf->MultiCell('', '', $txt, 1, 'L', false, 1, 0, '');
        $pdf->setFont('times', 'BI', 11);
        $pdf->Cell('103', '', 'Signature du représentant de Radio France :', 0, 0, 'L', false);
        $pdf->Cell('103', '', 'Signature du salarié : ', 0, 1, 'L', false);
        $y = $pdf->getY();
        $pdf->setFont('times', 'I', 11);
        $pdf->Image("img/signature.png", '30', '', 35, 17, 'png', '', 'C', true);
        $pdf->ln(17);
        $pdf->Cell('103', '', 'Nom du signataire :', 0, 0, 'L', false);
        $pdf->Cell('103', '', 'Nom du signataire :', 0, 1, 'L', false);
        $pdf->setFont('times', '', 11);
        $pdf->Cell('103', '', 'Cédric LANG-ROTH, rédacteur en chef', 0, 0, 'L', false);
        $pdf->Cell('103', '', $contrats->getPigiste()->getNom() . " " . $contrats->getPigiste()->getPrenom(), 0, 1, 'L', false);
        $pdf->Cell('103', '', 'Date : ' . date('d/m/Y'), 0, 0, 'L', false);
        $pdf->Cell('103', '', 'Date :', 0, 1, 'L', false);
        $pdf->setFont('times', 'I', 9);
        $pdf->MultiCell('', '', "Les éléments contractuels de votre collaboration, définis ci-dessus, sont complétés par les conditions générales d’engagement à Radio France figurant ci-après sur les pages du présent contrat.", 0, 'L', false, 0);
        $pdf->AddPage();


        $txt = "CONDITIONS GENERALES D'ENGAGEMENT";
        $pdf->setFont('times', 'B', '14');
        $pdf->ln(2);
        $pdf->Cell(204, 0, $txt, 1, 2, "C", false);

        $pdf->SetFont('times', '', 8, '', true);

        $txt = "Les présentes conditions générales ne peuvent en aucun cas porter atteinte à l'application des conditions d'emploi spéciales à certaines catégories, qui figurent dans des documents qui sont  tenus à la disposition des intéressés, dans les Services de Radio France. Sous cette réserve, le Contractant, en signant la présente lettre d'engagement, accepte les conditions générales d'engagement suivantes :

1. Le présent engagement constitue un contrat de travail à durée et objet déterminés. Il n'est donc en aucun cas renouvelable par tacite reconduction et il cesse de plein droit à la date mentionnée en page 1.

2. Le Contractant doit se présenter aux jours, lieux et heures indiqués par Radio France. Il doit, d'une manière générale, se conformer aux instructions qui lui sont données par les représentants qualifiés de Radio France.
Lors de sa première collaboration à Radio France, le Contractant doit compléter et signer la fiche de renseignements administratifs remise par  le Service de production et s'engage à signaler toute modification ultérieure de sa situation. Le Contractant doit signer la feuille de présence qui lui est présentée.

3. Le Contractant doit, avant la signature de son contrat, faire connaître à Radio France les dates et horaires des engagements qu'il aurait acceptés pour la période de travail qui lui  est proposée, ainsi que les engagements antérieurs qui pourraient restreindre, en ce qui le concerne, les utilisations des prestations prévues aux articles 4 et 7 ci-après. 
D'une manière générale, le Contractant ne doit pas accepter d'engagements qui seraient incompatibles avec l'accomplissement des obligations résultant du présent contrat. 
Dans le cas où les dispositions particulières du présent contrat prévoient une exclusivité pendant toute la durée de l'engagement et que le Contractant ne respecte pas cette exclusivité, le contrat est résilié de plein droit, sans préjudice de dommages-intérêts à la charge du Contractant. 
Le Contractant doit prévenir le Service de production intéressé dans les plus brefs délais et par tous moyens, en cas d'impossibilité d'exécution de la prestation et notamment en cas de maladie, afin de permettre à Radio France de prendre toutes dispositions utiles.

4. Les émissions pourront avoir lieu en présence ou non d'un public, être enregistrées et diffusées une ou plusieurs fois en direct ou en différé, en extraits ou intégralement, dans les programmes de Radio France par tous moyens et procédés, par voie hertzienne, câble et satellite ou tout autre procédé de télécommunication ou de télédiffusion et par tout procédé électronique ou informatique de communication au public, y compris internet et notamment sur son site en écoute à la demande et téléchargement (ex : podcast). Elles pourront être utilisées également pour des relais ou envois de programmes à d'autres organismes de radiodiffusion, pour des remises de copies  aux personnes ayant apporté une contribution intellectuelle, pour la présentation et le rappel des programmes, et, d'une manière générale, à l'occasion des différentes activités de Radio France.

5. Compte tenu de ses missions de service public et de sa responsabilité éditoriale, Radio France dispose de l'entière liberté de modifier la composition de ses programmes et notamment de différer ou supprimer la diffusion des émissions prévues, ce que le Contractant déclare accepter sans réserve. Radio France pourra effectuer, conformément  aux usages radiophoniques, sous réserve du respect du droit moral des auteurs, les coupures et montages nécessaires à la diffusion et à l'exploitation des émissions.

6. En signant le présent contrat, le Contractant s'engage : 
- à ne pas utiliser ni laisser  utiliser sa collaboration aux émissions de Radio France pour sa publicité personnelle ;
- à ne pas utiliser ni laisser utiliser par un tiers, quel qu'il soit, cette collaboration à des fins publicitaires, à quelque condition que ce soit ; 
- à ne pas participer, directement ou indirectement, à toute opération commerciale ou publicitaire qui serait réalisée par lui-même ou un tiers et qui aurait un lien direct avec l'émission à laquelle il apporte sa collaboration. 
Le Contractant s'engage à ne pas utiliser sa participation aux émissions de Radio France pour obtenir des marchés, contrats ou conventions de toute nature entre Radio France et une entreprise dans laquelle il posséderait des intérêts ou avec laquelle il aurait des liens directs ou indirects.

7. Dans l'hypothèse où le Contractant pourrait revendiquer la qualité d'auteur, il cède à Radio France en signant le présent engagement, les droits de représentation et de reproduction nécessaires à l'exploitation de ses prestations dans le cadre de l'ensemble des activités de Radio France telles que décrites à l'article 4 ci-dessus, et quel que soit le procédé ou le vecteur technique de reproduction et de diffusion utilisé, et ce, pour la durée totale de la propriété littéraire et artistique et pour le monde entier.

8. Les droits d'auteur éventuellement dus au Contractant sont exclusivement réglés dans le cadre des accords existant et/ou à venir entre Radio France et les sociétés d'auteurs (SACEM-SDRM-SACD-SCAM).

9. Le Contractant ne pourra en aucun cas, sauf accord écrit et préalable du Président de Radio France, faire usage personnellement ou autoriser des tiers à faire usage des thèmes, des principaux personnages ou de la formule des émissions ou de toute autre formule similaire, qu'il s'agisse de radiodiffusion ou d'une utilisation sous toute autre forme. Cette clause demeure valable après la cessation de la diffusion des émissions. Radio France est en tout état de cause propriétaire du titre des émissions.

10. Le Contractant affirme que les travaux ou prestations effectués par lui en application du présent contrat  ne portent en aucune manière atteinte aux droits détenus par des tiers et notamment aux droits des auteurs d'oeuvres protégées.

11. L'utilisation commerciale éventuelle, sous quelque forme que ce soit, graphique, sonore, y compris les formes numérisées etc., des prestations prévues dans le présent engagement est réservée à Radio France. Si le Contractant envisage l'une de ces utilisations, il devra impérativement se rapprocher de Radio France au préalable.

12. En application de l'article 7-2 du règlement intérieur affiché dans les locaux de Radio France, le Contractant est responsable des objets ou documents mis à sa disposition pour l'exécution du présent contrat. Il ne pourra les utiliser à des fins personnelles. Radio France se réserve le droit de demander le remboursement du montant de la perte ou du coût de la réparation en cas de détérioration volontaire.

13. Lorsque le Contractant fournit pour l'émission, un matériel ou un objet quel qu'il soit (enregistrement, texte inédit, etc.), il garantit que Radio France pourra l'utiliser librement pour tous les usages prévus dans le cadre du présent contrat. En cas de perte ou détérioration, et si Radio France en est responsable, le Contractant pourra être dédommagé.

14. Si le Contractant est producteur coordonnateur délégué, producteur d'émission délégué ou réalisateur, il doit envoyer avant l'émission (dans un délai raisonnable permettant la préparation de l'émission), le texte ou synopsis nécessaire et le plan de travail comportant notamment la distribution. Il doit se conformer au plan-type de l'émission et s'en tenir aux moyens qui ont été accordés par Radio France.

15. Le Contractant est tenu de donner à l'assistant de réalisation ou de production ou au régisseur toutes les indications permettant d'établir des déclarations complètes concernant les droits d'auteurs et droits voisins relatifs à l'émission. En l'absence d'assistant ou de régisseur, et si le Contractant est réalisateur, producteur coordonnateur délégué, producteur d'émission délégué, programmateur, présentateur ou animateur, il est responsable de l'établissement de ces déclarations.

16. Artistes-interprètes : Le Contractant autorise la fixation et la reproduction de sa prestation ainsi que toutes utilisations dans les conditions générales définies dans le présent contrat et le cas échéant dans la convention collective en vigueur à Radio France pour sa catégorie.
";

        $pdf->ln(3);
        $pdf->MultiCell(204, 0, $txt, 1, "J", false, 1, "", "", true, 0, false, true);
        $pdf->SetFont('times', '', 11);
        $pdf->ln(2);
        $pdf->Cell(103, 0, "Contrat n°" . $contrats->getNumero(), 0, 0, "L", false);
        $pdf->Cell(100, 0, "Page 2/2", 0, 0, "R", false);
        // Close and output PDF document
// This method has several options, check the source code documentation for more information.
        return $pdf->Output($contrats->getNumero() . '.pdf', 'D');
    }

}
