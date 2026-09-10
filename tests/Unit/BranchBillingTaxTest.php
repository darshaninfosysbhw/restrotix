<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\Billing\BillingCheckoutController;
use App\Models\Branch;
use App\Models\Table;
use PHPUnit\Framework\TestCase;

class BranchBillingTaxTest extends TestCase
{
    public function test_non_vat_checkout_ignores_saved_rate_and_client_tax_fallback(): void
    {
        foreach (['exclusive', 'inclusive'] as $setting) {
            $branch = new Branch(['is_vat_registered' => false, 'tax_rate' => 13, 'tax_setting' => $setting]);
            $table = new Table();
            $table->setRelation('branch', $branch);
            $controller = (new \ReflectionClass(BillingCheckoutController::class))->newInstanceWithoutConstructor();
            $context = (new \ReflectionMethod($controller, 'resolveBillingTaxContext'))->invoke($controller, $table, ['tax_rate_snapshot' => 13]);
            $totals = (new \ReflectionMethod($controller, 'calculateBillingTaxTotals'))->invoke($controller, 290.0, $context);
            $this->assertEquals(0, $totals['tax_amount']);
            $this->assertEquals(290, $totals['grand_total']);
            $this->assertEquals(0, $context['tax_rate_percent']);
            $this->assertEquals(13, $branch->tax_rate);
            $branch->is_vat_registered = true;
            $this->assertEquals(13, $branch->effective_tax_rate);
            $registeredContext = (new \ReflectionMethod($controller, 'resolveBillingTaxContext'))->invoke($controller, $table);
            $registeredTotals = (new \ReflectionMethod($controller, 'calculateBillingTaxTotals'))->invoke($controller, 290.0, $registeredContext);
            $this->assertEqualsWithDelta($setting === 'exclusive' ? 327.7 : 290, $registeredTotals['grand_total'], 0.001);
        }
    }
}
