<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            // 'https://oba-felix-fale.vercel.app',
            // 'http://localhost:5173',
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
