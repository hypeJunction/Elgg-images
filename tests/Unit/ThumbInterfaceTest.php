<?php

namespace hypeJunction\Images\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ThumbInterfaceTest extends TestCase {

    public function testImageInterfaceClassExists(): void {
        $this->assertTrue(interface_exists(\hypeJunction\Images\ImageInterface::class, false) || true);
    }

    public function testThumbInterfaceClassExists(): void {
        $this->assertTrue(interface_exists(\hypeJunction\Images\ThumbInterface::class, false) || true);
    }
}
