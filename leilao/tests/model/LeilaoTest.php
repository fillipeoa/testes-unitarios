<?php

namespace Leilao\Tests\Model;

use Leilao\Model\Lance;
use Leilao\Model\Leilao;
use Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        static::expectException(\DomainException::class);
        static::expectExceptionMessage('Usuário não pode propor 2 lances consecutivos');

        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        static::expectException(\DomainException::class);
        static::expectExceptionMessage('Usuário não pode propor mais de 5 lances por leilão');

        $leilao = new Leilao('Brasilia Amarela');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));

        $leilao->recebeLance(new Lance($joao, 6000));
    }

    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(
        int $qtdLances,
        Leilao $leilao,
        array $valores
    )
    {
        static::assertCount($qtdLances, $leilao->getLances());

        foreach($valores as $i => $valorEsperado){
            static::assertEquals($valorEsperado, $leilao->getLances()[$i]->getValor());
        }
    }

    public static function geraLances()
    {
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao2Lances = new Leilao('Fiat 147 0Km');
        $leilao2Lances->recebeLance(new Lance($joao, 1000));
        $leilao2Lances->recebeLance(new Lance($maria, 2000));

        $leilao1Lance = new Leilao('Fusca 1972 0Km');
        $leilao1Lance->recebeLance(new Lance($maria, 5000));

        return [
            '2-lances' => [2, $leilao2Lances, [1000,2000]],
            '1-lance' => [1, $leilao1Lance, [5000]]
        ];
    }
}