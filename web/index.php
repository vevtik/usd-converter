<?php
use Exchanger\Api\FixRate;
use Exchanger\Api\ApiManager;
use Exchanger\Api\EuropeanCentralBank;
use Exchanger\Api\Binance;


$euroCurrencies = ["JPY","BGN","CZK","DKK","GBP","HUF","PLN","RON","SEK","CHF","ISK","NOK"];
$binanceCurrencies = ["BTC", "BCH"];
/**
 * @author  Vladimir Dimitrischuck <vevtik@gmail.com>
 */
if (! empty($_POST) && ! empty($_POST['target'])) {
    require_once __DIR__ . '/../vendor/autoload.php';

    //Конфигурация
    $manager = new ApiManager();

    //Обмен в 1 итерацию
    $manager->addSupportPair(
        sprintf('#^USD_(%s)$#', implode('|', $euroCurrencies)),
        [new EuropeanCentralBank()]
    );
    //Другой провайдер в котром нет USD
    $manager->addSupportPair(
        sprintf('#^USDT_(%s)$#', implode('|', $binanceCurrencies)),
        [new Binance()]
    );

    //Фиксированый курс
    $fixRateApi = new FixRate();
    $fixRateApi->addSupportPair('USD_USDT', 1);

    $manager->addSupportPair('#^USD_USDT$#', [$fixRateApi]);

    //Обмен в несколько итераций
    $manager->addSupportPair(
        sprintf('#^USD_(%s)$#', implode('|', $binanceCurrencies)),
        [['USDT', '{target}']]
    );
    //Конец конфигурации

    header("Content-Type: text/json");
    try {
        $rate = $manager->getExchangeRate('USD', $_POST['target']);
        $result = json_encode(['rate'=> $rate, 'success'=> true]);
    } catch (\Exception $e) {
        $result = json_encode(['message'=> $e->getMessage(), 'success'=> false]);

    }

    exit($result);
}
$supportedCurrencies = array_merge($euroCurrencies, $binanceCurrencies);
include __DIR__ . '/demo_page.phtml';