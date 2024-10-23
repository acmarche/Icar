<?php

namespace AcMarche\Icar\Utils;

use proj4php\Point;
use proj4php\Proj;
use proj4php\Proj4php;

class CoordonateUtils
{
    public static function convertToGeolocalisation($x, $y)
    {
        $proj4 = new Proj4Php();

        // Define the Lambert72 projection (EPSG:31370)
        $lambert72 = new Proj('EPSG:31370', $proj4);

        // Define the WGS84 projection (EPSG:4326)
        $wgs84 = new Proj('EPSG:4326', $proj4);

        // Create a point object for the Lambert72 coordinates
        $pointSrc = new Point($x, $y, $lambert72);

        // Transform the point to WGS84
        $pointDst = $proj4->transform($wgs84, $pointSrc);

        return ["x" => $pointDst->x, "y" => $pointDst->y];
    }

    public static function lambert93ToWgs84(string $x, string $y): array
    {
        $b8 = 1 / 298.257222101;
        $b10 = sqrt(2 * $b8 - $b8 * $b8);
        $b16 = 0.7256077650532670;
        $x = number_format((float)$x, 10, '.', '') - 700000;
        $y = number_format((float)$y, 10, '.', '') - 12_655_612.0499;
        $gamma = atan(-$x / $y);
        $latiso = log(11_754_255.426096 / sqrt(($x * $x) + ($y * $y))) / $b16;
        $sinphiit = tanh($latiso + $b10 * atanh($b10 * sin(1)));

        for ($i = 0; $i != 6; $i++) {
            $sinphiit = tanh($latiso + $b10 * atanh($b10 * $sinphiit));
        }

        return ([
            'longitude' => ($gamma / $b16 + 3 / 180 * pi()) / pi() * 180,
            'latitude' => asin($sinphiit) / pi() * 180,
        ]);
    }

    public function lambert2gps($x, $y, $lambert): array
    {
        $lamberts = [
            "LambertI" => 0,
            "LambertII" => 1,
            "LambertIII" => 2,
            "LamberIV" => 3,
            "LambertIIExtend" => 4,
            "Lambert93" => 5,
        ];
        $index = $lamberts[$lambert];
        $ntabs = [0.7604059656, 0.7289686274, 0.6959127966, 0.6712679322, 0.7289686274, 0.7256077650];
        $ctabs = [11_603_796.98, 11_745_793.39, 11_947_992.52, 12_136_281.99, 11_745_793.39, 11_754_255.426];
        $Xstabs = [600000.0, 600000.0, 600000.0, 234.358, 600000.0, 700000.0];
        $Ystabs = [5_657_616.674, 6_199_695.768, 6_791_905.085, 7_239_161.542, 8_199_695.768, 12_655_612.050];

        $n = $ntabs [$index];
        $c = $ctabs [$index];            // En mètres
        $Xs = $Xstabs[$index];          // En mètres
        $Ys = $Ystabs[$index];          // En mètres
        $l0 = 0.0;                    //correspond à la longitude en radian de Paris (2°20'14.025" E) par rapport à Greenwich
        $e = 0.08248325676;           //e du NTF (on le change après pour passer en WGS)
        $eps = 0.00001;     // précision


        /***********************************************************
         *  coordonnées dans la projection de Lambert 2 à convertir *
         ************************************************************/
        $X = $x;
        $Y = $y;

        /*
         * Conversion Lambert 2 -> NTF géographique : ALG0004
         */
        $R = Sqrt((($X - $Xs) * ($X - $Xs)) + (($Y - $Ys) * ($Y - $Ys)));
        $g = Atan(($X - $Xs) / ($Ys - $Y));

        $l = $l0 + ($g / $n);
        $L = -(1 / $n) * Log(Abs($R / $c));


        $phi0 = 2 * Atan(Exp($L)) - (pi() / 2.0);
        $phiprec = $phi0;
        $phii = 2 * Atan((((1 + $e * Sin($phiprec)) / (1 - $e * Sin($phiprec))) ** ($e / 2.0) * Exp($L))) - (pi(
                ) / 2.0);

        while (Abs($phii - $phiprec) >= $eps) {
            $phiprec = $phii;
            $phii = 2 * Atan((((1 + $e * Sin($phiprec)) / (1 - $e * Sin($phiprec))) ** ($e / 2.0) * Exp($L))) - (pi(
                    ) / 2.0);
        }

        $phi = $phii;

        /*
         * Conversion NTF géogra$phique -> NTF cartésien : ALG0009
         */
        $a = 6_378_249.2;
        $h = 100;         // En mètres

        $N = $a / ((1 - ($e * $e) * (Sin($phi) * Sin($phi))) ** 0.5);
        $X_cart = ($N + $h) * Cos($phi) * Cos($l);
        $Y_cart = ($N + $h) * Cos($phi) * Sin($l);
        $Z_cart = (($N * (1 - ($e * $e))) + $h) * Sin($phi);

        /*
         * Conversion NTF cartésien -> WGS84 cartésien : ALG0013
         */

        // Il s'agit d'une simple translation
        $XWGS84 = $X_cart - 168;
        $YWGS84 = $Y_cart - 60;
        $ZWGS84 = $Z_cart + 320;


        /*
         * Conversion WGS84 cartésien -> WGS84 géogra$phique : ALG0012
         */

        $l840 = 0.04079234433;    // 0.04079234433 pour passer dans un référentiel par rapport au méridien
        // de Greenwich, sinon mettre 0

        $e = 0.08181919106;              // On change $e pour le mettre dans le système WGS84 au lieu de NTF
        $a = 6_378_137.0;

        $P = Sqrt(($XWGS84 * $XWGS84) + ($YWGS84 * $YWGS84));

        $l84 = $l840 + Atan($YWGS84 / $XWGS84);

        $phi840 = Atan(
            $ZWGS84 / ($P * (1 - (($a * $e * $e))
                    / Sqrt(($XWGS84 * $XWGS84) + ($YWGS84 * $YWGS84) + ($ZWGS84 * $ZWGS84)))),
        );

        $phi84prec = $phi840;

        $phi84i = Atan(
            ($ZWGS84 / $P) / (1 - (($a * $e * $e * Cos($phi84prec))
                    / ($P * Sqrt(1 - $e * $e * (Sin($phi84prec) * Sin($phi84prec)))))),
        );

        while (Abs($phi84i - $phi84prec) >= $eps) {
            $phi84prec = $phi84i;
            $phi84i = Atan(
                ($ZWGS84 / $P) / (1 - (($a * $e * $e * Cos($phi84prec))
                        / ($P * Sqrt(1 - (($e * $e) * (Sin($phi84prec) * Sin($phi84prec))))))),
            );
        }

        $phi84 = $phi84i;

        return [$phi84 * 180.0 / pi(), $l84 * 180.0 / pi()];
    }

    public function lambertI($x, $y): array
    {
        return $this->lambert2gps($x, $y, "LambertI");
    }

    public function lambertII($x, $y): array
    {
        return $this->lambert2gps($x, $y, "LambertII");
    }

    public function lambertIII($x, $y): array
    {
        return $this->lambert2gps($x, $y, "LambertIII");
    }

    public function lamberIV($x, $y): array
    {
        return $this->lambert2gps($x, $y, "LamberIV");
    }

    public function lambertIIExtend($x, $y): array
    {
        return $this->lambert2gps($x, $y, "LambertIIExtend");
    }

    public function lambert93($x, $y): array
    {
        return $this->lambert2gps($x, $y, "Lambert93");
    }
}
