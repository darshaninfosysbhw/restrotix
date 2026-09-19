<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Tenant;
use PHPUnit\Framework\TestCase;

class IdentityMaskingTest extends TestCase
{
    public function test_tenant_override_takes_precedence_over_plan(): void
    {
        $tenant = new Tenant(['custom_identity_masking' => false]);
        $tenant->setRelation('plan', new Plan(['allow_identity_masking' => true]));

        $this->assertFalse($tenant->canMaskIdentity());

        $tenant->custom_identity_masking = true;
        $tenant->plan->allow_identity_masking = false;

        $this->assertTrue($tenant->canMaskIdentity());
    }

    public function test_tenant_falls_back_to_plan_when_override_is_null(): void
    {
        $tenant = new Tenant(['custom_identity_masking' => null]);
        $tenant->setRelation('plan', new Plan(['allow_identity_masking' => true]));

        $this->assertTrue($tenant->canMaskIdentity());
    }

    public function test_branch_titles_respect_scope_and_tenant_capability(): void
    {
        $tenant = new Tenant(['custom_identity_masking' => true]);
        $tenant->company_name = 'Real Restaurant Group';
        $branch = new Branch([
            'branch_name' => 'Downtown Kitchen',
            'display_name' => 'City Cafe',
            'mask_scope' => 'public_only',
        ]);
        $branch->setRelation('tenant', $tenant);

        $this->assertSame('City Cafe', $branch->customer_title);
        $this->assertSame('Downtown Kitchen', $branch->system_title);
        $this->assertSame('City Cafe', $branch->customer_brand_name);
        $this->assertSame('', $branch->customer_branch_subtitle);

        $branch->mask_scope = 'everywhere';

        $this->assertSame('City Cafe', $branch->customer_title);
        $this->assertSame('City Cafe', $branch->system_title);
    }

    public function test_customer_title_uses_guest_check_fallback(): void
    {
        $tenant = new Tenant(['custom_identity_masking' => true]);
        $branch = new Branch([
            'branch_name' => 'Downtown Kitchen',
            'display_name' => null,
            'mask_scope' => 'public_only',
        ]);
        $branch->setRelation('tenant', $tenant);

        $this->assertSame('Guest Check', $branch->customer_title);
        $this->assertSame('Downtown Kitchen', $branch->system_title);
    }
}
