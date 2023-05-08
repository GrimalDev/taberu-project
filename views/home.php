<?php
    session_start();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>TaBeRu</title>

    <link rel="stylesheet" href="../style/style-home.css" type="text/css">
    <link rel="shortcut icon" type="image/jpg" href="../style/media/TaBeRu-solid-fit.png"/>

    <script defer src="../scripts/script-general.js" type="application/javascript"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/es6-promise/dist/es6-promise.auto.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.5.4/jquery.imagemapster.min.js"></script>
    <script type="application/javascript" src="../scripts/script-home-map.js"></script>

</head>
<body>

    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/header.php')?>

    <main>
        <h1>
            Bienvenue sur<br />
            <img  class="title-noodles left" src="../style/media/food-images/noodles.png" alt="noodles">
                <strong>TaBeRu</strong>
            <img class="title-noodles right" src="../style/media/food-images/noodles.png" alt="noodles"><br />
            Un blog communautaire de recettes asiatiques
        </h1>
        <div id="map-title-container">
            <span>carte interactive des recettes</span>
        </div>
        <div id="map-image-container">
            <img id="img_ID" src="../style/media/Asia-Map.png" usemap="#map" alt="asia map">
        </div>
        <map id="map_ID" name="map">
            <area target="_self" alt="chine" title="chine" href="https://taberu.baptistegrimaldi.info/recettes#chine" coords="1914,298,1829,331,1747,383,1698,432,1649,508,1632,573,1636,645,1639,704,1669,767,1698,845,1731,904,1757,953,1796,1013,1852,1075,1891,1114,1944,1157,1996,1190,2042,1213,2098,1239,2170,1262,2235,1272,2301,1278,2379,1281,2435,1278,2514,1272,2596,1252,2674,1235,2746,1213,2819,1190,2884,1163,2923,1121,2923,1078,2904,1026,2887,990,2864,940,2848,898,2828,849,2825,806,2845,770,2884,744,2927,731,2956,737,2969,763,2972,796,2976,839,2989,875,2999,908,3015,931,3031,953,3051,970,3087,960,3107,921,3110,858,3087,816,3061,786,3048,740,3090,714,3140,681,3169,655,3192,626,3182,586,3179,547,3208,521,3241,471,3271,426,3290,386,3274,357,3225,347,3179,353,3143,353,3107,344,3087,304,3071,255,3054,209,3035,160,3002,124,2966,104,2927,85,2887,91,2838,108,2802,134,2766,164,2730,190,2694,213,2619,275,2567,288,2501,301,2449,282,2413,255,2370,216,2328,177,2301,150,2275,131,2246,137,2220,144,2203,170,2197,196,2193,236,2190,265,2180,282,2147,288,2108,282,2062,272,1997,268,1961,275" shape="poly">
            <area target="_self" alt="inde" title="inde" href="https://taberu.baptistegrimaldi.info/recettes#inde" coords="1512,1409,1479,1422,1436,1432,1404,1438,1384,1448,1417,1455,1436,1475,1456,1504,1463,1543,1473,1602,1492,1694,1515,1773,1535,1832,1564,1878,1600,1891,1626,1884,1653,1861,1669,1816,1689,1760,1708,1691,1735,1612,1767,1566,1807,1527,1853,1484,1895,1445,1846,1435,1807,1435,1780,1419,1754,1396,1748,1373,1712,1370,1679,1363,1649,1356,1630,1353,1623,1330,1604,1314,1577,1304,1535,1314,1535,1334,1538,1353,1541,1376,1535,1396" shape="poly">
            <area target="_self" alt="thailand" title="thailand" href="https://taberu.baptistegrimaldi.info/recettes#thailand" coords="2167,1435,2137,1449,2137,1489,2137,1521,2153,1557,2173,1580,2189,1613,2205,1636,2212,1672,2215,1715,2212,1741,2205,1767,2192,1787,2186,1810,2186,1849,2192,1889,2205,1931,2225,1971,2238,1990,2258,1997,2291,2016,2330,2026,2360,2030,2369,2010,2356,1987,2340,1964,2314,1941,2287,1915,2261,1872,2248,1823,2241,1784,2245,1751,2271,1741,2294,1748,2314,1764,2323,1797,2340,1751,2353,1725,2363,1689,2369,1659,2376,1633,2379,1597,2376,1557,2366,1531,2330,1518,2294,1518,2261,1528,2225,1544,2225,1508,2225,1472,2219,1443,2189,1433" shape="poly">
            <area target="_self" alt="japon" title="japon" href="https://taberu.baptistegrimaldi.info/recettes#japon" coords="3379,681,3386,704,3396,733,3396,763,3383,795,3373,825,3347,851,3327,815,3304,851,3301,871,3291,900,3278,917,3258,923,3209,927,3183,940,3163,956,3133,989,3127,1015,3117,1045,3120,1074,3127,1097,3140,1117,3160,1117,3173,1097,3183,1071,3196,1045,3219,1038,3261,1032,3284,1025,3310,1009,3333,995,3356,976,3376,959,3409,946,3442,940,3458,920,3461,897,3461,871,3455,845,3451,818,3455,779,3478,756,3494,736,3517,704,3481,697,3491,674,3524,651,3556,641,3586,632,3606,618,3606,592,3589,576,3570,550,3543,527,3514,507,3481,497,3455,497,3442,517,3442,569,3432,612,3419,628,3402,641,3389,658" shape="poly">
        </map>

    </main>

    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__).'/footer.html')?>
</body>
</html>