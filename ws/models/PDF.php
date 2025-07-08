<?php
// ws/models/PDF.php
require_once __DIR__.'/../vendor/autoload.php';

use Fpdf\Fpdf;

class PDF extends Fpdf {
    public function generatePretPDF($pretData, $etablissement) {
        $this->AddPage();
        
        // 1. En-tête
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,utf8_decode('ATTESTATION DE PRÊT N° PR-2025-00042'),0,1,'C');
        $this->Ln(10);

        // 2. Informations client
        $this->SetFont('Arial','B',12);
        $this->Cell(0,6,utf8_decode('Établissement financier : '.$etablissement),0,1);
        $this->Cell(0,10,utf8_decode('1. Informations Client'),0,1);
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,utf8_decode('Nom : '.$pretData['client_nom']),0,1);
        $this->Cell(0,6,utf8_decode('Email : '.$pretData['client_email']),0,1);
        $this->Cell(0,6,utf8_decode('Téléphone : '.$pretData['client_telephone']),0,1);
        $this->Ln(10);

        // 3. Détails du prêt
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,utf8_decode('2. Détails du Prêt'),0,1);
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,utf8_decode('Type de prêt : '.$pretData['type_pret']),0,1);
        $this->Cell(0,6,utf8_decode('Montant : '.$pretData['montant_formatted'].' Ar'),0,1);
        $this->Cell(0,6,utf8_decode('Taux d\'intérêt : '.$pretData['taux_interet_annuel'].'%'),0,1);
        $this->Cell(0,6,utf8_decode('Taux d\'assurance : '.$pretData['taux_assurance_annuel'].'%'),0,1);
        $this->Cell(0,6,utf8_decode('Durée : '.$pretData['duree_mois'].' mois'),0,1);
        $this->Cell(0,6,utf8_decode('Date début : '.$pretData['date_debut_formatted']),0,1);
        $this->Cell(0,6,utf8_decode('Date fin : '.$pretData['date_fin_formatted']),0,1);

        $this->Ln(10);
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,utf8_decode('TABLEAU D\'ÉCHÉANCIER'),0,1,'C');
        $this->SetFont('Arial','B',10);
        $this->Cell(20,7,utf8_decode('Mois'),1);
        $this->Cell(30,7,utf8_decode('Mensualité'),1);
        $this->Cell(30,7,utf8_decode('Capital'),1);
        $this->Cell(25,7,utf8_decode('Intérêt'),1);
        $this->Cell(30,7,utf8_decode('Assurance'),1);
        $this->Cell(30,7,utf8_decode('Reste dû'),1);
        $this->Ln();

        $this->SetFont('Arial','',9);

        $capital = $pretData['montant_emprunt'];
        $duree = $pretData['duree_mois'];
        $taux = $pretData['taux_interet_annuel'];
        $assurance = $pretData['taux_assurance_annuel'];
        $mensualite = ($capital * (1 + $taux / 100)) / $duree;
        $interet_mensuel = ($capital * ($taux / 100)) / $duree;
        $assurance_mensuelle = ($capital * $assurance / 100) / 12;

        $reste_du = $capital;
        $total_rembourse = 0;

        for ($i = 1; $i <= $duree; $i++) {
            $capital_mensuel = $mensualite - $interet_mensuel - $assurance_mensuelle;
            $reste_du -= $capital_mensuel;
            $total_rembourse += $mensualite;

            $this->Cell(20,6,$i,1);
            $this->Cell(30,6,number_format($mensualite,2,',',' '),1);
            $this->Cell(30,6,number_format($capital_mensuel,2,',',' '),1);
            $this->Cell(25,6,number_format($interet_mensuel,2,',',' '),1);
            $this->Cell(30,6,number_format($assurance_mensuelle,2,',',' '),1);
            $this->Cell(30,6,number_format(max($reste_du,0),2,',',' '),1);
            $this->Ln();
        }

        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->Cell(0,6,utf8_decode('Total à rembourser : '.number_format($total_rembourse,2,',',' ').' Ar'),0,1);

        $this->Ln(10);
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,utf8_decode('Fait à Antananarivo, le '.date('d/m/Y')),0,1);
        $this->Ln(15);
        $this->Cell(90,6,utf8_decode('Signature client'),0,0,'C');
        $this->Cell(90,6,utf8_decode('Signature responsable'),0,1,'C');
    }
}
