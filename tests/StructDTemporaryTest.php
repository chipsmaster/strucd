<?php

namespace Chipsmaster\StrucD;

use PHPUnit\Framework\TestCase;

class StructDTemporaryTest extends TestCase
{
    // TODO this is temporary


    public function testGeneral()
    {
        $f1 = [
            'k1' => 'v1',
            'k2' => [
                'sk1' => 'sv1',
                'sk2' => 34,
                'sk3' => [ 'ssv1', 'ssv2' ],
            ],
            'k3' => true,
            'k4' => [ 'k4v1', 'k4v2' ],
        ];

        $s = StrucDHandler::getDefault();


        // Quick general cases (temporarily)
        $this->assertSame([], $s->merge());
        $this->assertSame($f1, $s->merge($f1));
        $this->assertSame($f1, $s->merge([], $f1));
        $this->assertSame([
            'k1' => 'v1',
            'k2' => [
                'sk1' => 'sv1-2',
                'sk2' => 34,
                'sk3' => [ 'ssv1', 'ssv2' ],
                'sk4' => 'aaa',
            ],
            'k3' => [ 'a' => 'x' ],
            'k4' => null,
            'k5' => 'v5',
        ], $s->merge($f1, [
            'k2' => [
                'sk1' => 'sv1-2',
                'sk4' => 'aaa',
            ],
            'k3' => [ 'a' => 'x' ],
            'k4' => null,
            'k5' => 'v5',
        ]));

        $this->assertSame('v1', $s->get($f1, 'k1'));
        $this->assertSame([
            'sk1' => 'sv1',
            'sk2' => 34,
            'sk3' => [ 'ssv1', 'ssv2' ],
        ], $s->get($f1, 'k2'));
        $this->assertSame(34, $s->get($f1, 'k2/sk2'));
        $this->assertSame(null, $s->get($f1, 'k2/skz'));


        $w = new StrucDWrapper($f1);
        $this->assertSame($f1, $w->getData());
        $w->addData([
            'k2' => [
                'sk2' => 'sv2'
            ],
            'k5' => 'xxx',
        ]);
        $this->assertSame([
            'k1' => 'v1',
            'k2' => [
                'sk1' => 'sv1',
                'sk2' => 'sv2',
                'sk3' => [ 'ssv1', 'ssv2' ],
            ],
            'k3' => true,
            'k4' => [ 'k4v1', 'k4v2' ],
            'k5' => 'xxx',
        ], $w->getData());
        $this->assertSame('sv2', $w->get('k2/sk2'));
        $this->assertSame([ 'ssv1', 'ssv2' ], $w->get('k2/sk3'));

        $w->removeData('k2/sk2');
        $w->removeData('k2/sk1/xxx');
        $w->removeData('k1');
        $w->removeData('k3/xxx');
        $w->removeData('k6');
        $this->assertSame([
            'k2' => [
                'sk1' => 'sv1',
                'sk3' => [ 'ssv1', 'ssv2' ],
            ],
            'k3' => true,
            'k4' => [ 'k4v1', 'k4v2' ],
            'k5' => 'xxx',
        ], $w->getData());
    }
}
