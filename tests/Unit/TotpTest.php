<?php

namespace Tests\Unit;

use App\Support\Totp;
use PHPUnit\Framework\TestCase;

class TotpTest extends TestCase
{
    /** RFC 6238 appendix B test vectors (SHA-1, truncated to 6 digits). */
    public function test_matches_rfc_6238_vectors(): void
    {
        $secret = Totp::base32Encode('12345678901234567890');

        $this->assertSame('287082', Totp::code($secret, 59));
        $this->assertSame('081804', Totp::code($secret, 1111111109));
        $this->assertSame('005924', Totp::code($secret, 1234567890));
        $this->assertSame('279037', Totp::code($secret, 2000000000));
    }

    public function test_verify_allows_one_step_of_drift_only(): void
    {
        $secret = Totp::generateSecret();
        $now = 1_700_000_000;

        $this->assertTrue(Totp::verify($secret, Totp::code($secret, $now), $now));
        $this->assertTrue(Totp::verify($secret, Totp::code($secret, $now - 30), $now));
        $this->assertFalse(Totp::verify($secret, Totp::code($secret, $now - 120), $now));
        $this->assertFalse(Totp::verify($secret, 'abcdef', $now));
    }

    public function test_base32_round_trip(): void
    {
        $bytes = random_bytes(10);
        $this->assertSame($bytes, Totp::base32Decode(Totp::base32Encode($bytes)));
        $this->assertSame(16, strlen(Totp::generateSecret(10)));
    }
}
