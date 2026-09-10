@extends('core.layouts.front')

@section('title', 'Terms of Use')

@section(
    'meta_description',
    'Read the Terms of Use governing access to and use of Restrotix restaurant management, POS, marketplace and related services.'
)

@section('content')

<style>
    /*
    |--------------------------------------------------------------------------
    | Restrotix - Terms of Use
    |--------------------------------------------------------------------------
    | Simple legal-document layout inspired by standard SaaS legal pages.
    | Scoped only to this page.
    */

    .restrotix-legal-page {
        background: #ffffff;
        color: #374151;
        width: 100%;
        min-height: 100vh;
        padding: 55px 20px 80px;
    }

    .restrotix-legal-container {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    .restrotix-legal-header {
        margin-bottom: 38px;
        text-align: center;
        text-decoration: underline;
    }

    .restrotix-legal-title {
        margin: 0 0 12px;
        color: #111827;
        font-size: 32px;
        line-height: 1.25;
        font-weight: 700;
        text-transform: uppercase;
    }

    .restrotix-legal-updated {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .restrotix-legal-content {
        color: #374151;
        font-size: 15px;
        line-height: 1.8;
    }

    .restrotix-legal-content h2 {
        margin: 34px 0 12px;
        color: #111827;
        font-size: 18px;
        line-height: 1.5;
        font-weight: 700;
    }

    .restrotix-legal-content h2:first-of-type {
        margin-top: 30px;
    }

    .restrotix-legal-content h3 {
        margin: 24px 0 10px;
        color: #111827;
        font-size: 16px;
        line-height: 1.5;
        font-weight: 600;
    }

    .restrotix-legal-content p {
        margin: 0 0 15px;
        color: #374151;
        font-size: 15px;
        line-height: 1.8;
    }

    .restrotix-legal-content ul {
        margin: 6px 0 18px;
        padding-left: 26px;
        list-style-type: disc;
    }

    .restrotix-legal-content ol {
        margin: 6px 0 18px;
        padding-left: 26px;
    }

    .restrotix-legal-content li {
        margin: 5px 0;
        padding-left: 3px;
        color: #374151;
        font-size: 15px;
        line-height: 1.75;
    }

    .restrotix-legal-content strong {
        color: #1f2937;
        font-weight: 600;
    }

    .restrotix-legal-content a {
        color: #db0913;
        text-decoration: none;
        font-weight: 500;
    }

    .restrotix-legal-content a:hover {
        color: #bd0710;
        text-decoration: underline;
    }

    .restrotix-legal-divider {
        width: 100%;
        height: 1px;
        margin: 30px 0 0;
        background: #e5e7eb;
        border: 0;
    }

    .restrotix-legal-footer {
        margin-top: 45px;
        padding-top: 25px;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 14px;
    }

    .restrotix-legal-footer p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .restrotix-legal-page {
            padding: 35px 18px 60px;
        }

        .restrotix-legal-title {
            font-size: 26px;
        }

        .restrotix-legal-content {
            font-size: 14px;
        }

        .restrotix-legal-content p,
        .restrotix-legal-content li {
            font-size: 14px;
            line-height: 1.75;
        }

        .restrotix-legal-content h2 {
            margin-top: 28px;
            font-size: 17px;
        }
    }
</style>


<section class="restrotix-legal-page">

    <div class="restrotix-legal-container">

        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <header class="restrotix-legal-header">

            <h1 class="restrotix-legal-title">
                Terms of Use
            </h1>

           

        </header>


        {{-- =====================================================
            CONTENT
        ====================================================== --}}
        <div class="restrotix-legal-content">

            <p>
                Welcome to <strong>Restrotix</strong>.
            </p>

            <p>
                These Terms of Use (“Terms”) govern your access to and use of
                the Restrotix website, applications, software, dashboards,
                point-of-sale services, restaurant management tools,
                marketplace services, integrations, APIs, and other related
                products and services (collectively, the “Services”).
            </p>

            <p>
                By creating an account, starting a trial, subscribing to a
                plan, accessing the Services, or otherwise using Restrotix,
                you agree to be bound by these Terms.
            </p>

            <p>
                If you do not agree to these Terms, you must not access or use
                the Services.
            </p>


            {{-- 1 --}}
            <h2>
                1. About Restrotix
            </h2>

            <p>
                Restrotix provides technology solutions designed to help
                restaurants, cafés, hotels, food-service businesses,
                suppliers, and related businesses manage their operations.
            </p>

            <p>
                Depending on your subscription, location, configuration, and
                availability, Restrotix may provide features including:
            </p>

            <ul>
                <li>Point of Sale (POS) and billing</li>
                <li>Menu and digital menu management</li>
                <li>Inventory and stock management</li>
                <li>Order management</li>
                <li>Restaurant and branch management</li>
                <li>Multi-branch operations</li>
                <li>Staff and operational workflows</li>
                <li>Reports and analytics</li>
                <li>Supplier and procurement management</li>
                <li>Marketplace Connect services</li>
                <li>Purchase orders and supplier interactions</li>
                <li>Customer management</li>
                <li>Third-party integrations</li>
                <li>Notifications and communication tools</li>
            </ul>

            <p>
                Certain features may only be available under specific
                subscription plans.
            </p>


            {{-- 2 --}}
            <h2>
                2. Eligibility and Business Use
            </h2>

            <p>
                You must be legally capable of entering into a binding
                agreement to use Restrotix.
            </p>

            <p>
                If you use Restrotix on behalf of a company, restaurant,
                hotel, supplier, partnership, organization, or other legal
                entity, you confirm that you have authority to accept these
                Terms on behalf of that entity.
            </p>

            <p>
                You are responsible for ensuring that your use of the Services
                complies with applicable laws, licensing requirements, tax
                requirements, and industry regulations.
            </p>


            {{-- 3 --}}
            <h2>
                3. Account Registration
            </h2>

            <p>
                You may be required to create an account to access certain
                Restrotix Services. You agree to provide accurate, current,
                and complete information and to keep your information updated.
            </p>

            <p>
                You are responsible for:
            </p>

            <ul>
                <li>
                    Maintaining the confidentiality of your login credentials
                </li>

                <li>
                    Protecting passwords, OTPs, authentication codes, and
                    devices
                </li>

                <li>
                    Managing users and permissions within your organization
                </li>

                <li>
                    Activities carried out through authorized accounts
                </li>
            </ul>

            <p>
                You should notify Restrotix promptly if you believe your
                account has been compromised or accessed without
                authorization.
            </p>


            {{-- 4 --}}
            <h2>
                4. Organization, Branch and Staff Accounts
            </h2>

            <p>
                Business accounts may allow administrators to create branches,
                employees, users, roles and access permissions.
            </p>

            <p>
                The organization administrator is responsible for deciding
                which users may access business information and features.
            </p>


            {{-- 5 --}}
            <h2>
                5. Subscription Plans
            </h2>

            <p>
                Restrotix may offer free trials, paid subscriptions,
                enterprise arrangements, add-ons, or separately priced
                services.
            </p>

            <p>
                Features, users, branches, integrations, storage, support and
                usage limits may vary depending on the selected plan.
            </p>

            <p>
                Restrotix may introduce or modify subscription plans from time
                to time, subject to applicable laws and existing contractual
                commitments.
            </p>


            {{-- 6 --}}
            <h2>
                6. Free Trials
            </h2>

            <p>
                Restrotix may provide free trials for eligible users. Trial
                features and limitations may differ from paid plans.
            </p>

            <p>
                Unless otherwise communicated, access to paid features may
                stop after the trial period unless an eligible subscription is
                activated.
            </p>


            {{-- 7 --}}
            <h2>
                7. Fees, Billing and Taxes
            </h2>

            <p>
                You agree to pay applicable fees associated with Services you
                purchase.
            </p>

            <p>
                Prices may be exclusive or inclusive of taxes depending on
                the quotation, invoice, checkout page or subscription
                agreement.
            </p>

            <p>
                Payments may be handled through authorized banks, payment
                gateways, wallets, or other financial service providers.
            </p>


            {{-- 8 --}}
            <h2>
                8. Renewal and Cancellation
            </h2>

            <p>
                Recurring subscriptions may renew according to the billing
                period selected unless cancelled or otherwise agreed.
            </p>

            <p>
                Cancellation generally prevents future renewal and does not
                automatically create a refund entitlement for a billing period
                already paid unless required by law, provided under your plan,
                or otherwise agreed in writing.
            </p>


            {{-- 9 --}}
            <h2>
                9. Restaurant Transactions and POS Records
            </h2>

            <p>
                Restrotix provides tools that may create bills, orders,
                invoices, kitchen tickets, inventory movements, payment
                records, and business reports.
            </p>

            <p>
                The business remains responsible for verifying:
            </p>

            <ul>
                <li>Prices and menu information</li>
                <li>Discounts and quantities</li>
                <li>Applicable taxes</li>
                <li>Customer orders</li>
                <li>Payments</li>
                <li>Accounting records</li>
                <li>Government-compliant invoices</li>
                <li>Financial and regulatory reporting</li>
            </ul>

            <p>
                Restrotix does not replace professional accounting, financial,
                tax, or legal advice.
            </p>


            {{-- 10 --}}
            <h2>
                10. Marketplace and Supplier Services
            </h2>

            <p>
                Where Restrotix Marketplace Connect or procurement features
                are available, Restrotix may provide technology that enables
                restaurants, buyers and suppliers to interact.
            </p>

            <p>
                Unless expressly stated otherwise, Restrotix is not the
                manufacturer or physical seller of third-party goods listed
                by independent suppliers.
            </p>

            <p>
                Suppliers remain responsible for:
            </p>

            <ul>
                <li>Product descriptions</li>
                <li>Pricing</li>
                <li>Availability</li>
                <li>Quality</li>
                <li>Quantity and specifications</li>
                <li>Legal and regulatory compliance</li>
                <li>Delivery obligations</li>
                <li>Applicable warranties</li>
                <li>Taxes and invoices</li>
            </ul>


            {{-- 11 --}}
            <h2>
                11. Customer and Business Data
            </h2>

            <p>
                You retain ownership of business information and content that
                you lawfully submit to Restrotix.
            </p>

            <p>
                You grant Restrotix permission to host, process, transmit,
                analyze, secure and back up such information as reasonably
                necessary to provide and improve the Services.
            </p>


            {{-- 12 --}}
            <h2>
                12. Personal Data
            </h2>

            <p>
                Personal information processed through Restrotix is handled
                in accordance with our

                <a href="{{ route('policy.privacy') }}">
                    Privacy Policy
                </a>.
            </p>


            {{-- 13 --}}
            <h2>
                13. Acceptable Use
            </h2>

            <p>
                You must not use Restrotix to:
            </p>

            <ul>
                <li>Violate applicable laws</li>
                <li>Commit fraud</li>
                <li>Access another account without authorization</li>
                <li>Upload viruses or malicious software</li>
                <li>Bypass platform security</li>
                <li>Attack or interfere with Restrotix infrastructure</li>
                <li>Misrepresent your identity or organization</li>
                <li>Upload unlawful or infringing content</li>
                <li>Violate another person's privacy</li>
                <li>Abuse APIs, integrations or messaging systems</li>
            </ul>


            {{-- 14 --}}
            <h2>
                14. Third-Party Services and Integrations
            </h2>

            <p>
                Restrotix may integrate with payment gateways, messaging
                providers, delivery systems, hardware devices, cloud services,
                accounting platforms, APIs and other third-party services.
            </p>

            <p>
                Third-party services operate independently and may have their
                own terms and privacy policies.
            </p>


            {{-- 15 --}}
            <h2>
                15. Hardware and Devices
            </h2>

            <p>
                Some functionality may depend on printers, POS terminals,
                tablets, scanners, kitchen displays, networking equipment, or
                other compatible hardware.
            </p>

            <p>
                Restrotix does not guarantee the operation of independent
                third-party hardware unless expressly covered by a separate
                agreement or warranty.
            </p>


            {{-- 16 --}}
            <h2>
                16. Intellectual Property
            </h2>

            <p>
                Restrotix and its licensors retain all rights relating to the
                Restrotix platform, including software, source code, designs,
                interfaces, logos, trademarks, documentation, databases,
                graphics and proprietary technology.
            </p>

            <p>
                Your subscription gives you a limited right to use the
                Services. It does not transfer ownership of Restrotix
                intellectual property.
            </p>


            {{-- 17 --}}
            <h2>
                17. Feedback
            </h2>

            <p>
                Suggestions, recommendations and feature requests submitted
                to Restrotix may be used to develop and improve the Services
                without an obligation to compensate the person submitting
                them.
            </p>


            {{-- 18 --}}
            <h2>
                18. Service Availability
            </h2>

            <p>
                We work to maintain reliable Services but cannot guarantee
                uninterrupted or error-free availability at all times.
            </p>

            <p>
                Availability may be affected by:
            </p>

            <ul>
                <li>Maintenance</li>
                <li>Internet or network failures</li>
                <li>Third-party service failures</li>
                <li>Hardware failures</li>
                <li>Cybersecurity incidents</li>
                <li>Government restrictions</li>
                <li>Natural disasters</li>
                <li>Other events outside reasonable control</li>
            </ul>


            {{-- 19 --}}
            <h2>
                19. Backup and Data Protection
            </h2>

            <p>
                Restrotix may maintain reasonable backup and recovery
                procedures. Businesses should also maintain copies of
                important accounting, regulatory, tax and operational
                information where appropriate.
            </p>


            {{-- 20 --}}
            <h2>
                20. Suspension and Termination
            </h2>

            <p>
                Restrotix may suspend or terminate access where reasonably
                necessary because of non-payment, material breach, fraud,
                security risks, unlawful activities, misuse, or legal
                requirements.
            </p>


            {{-- 21 --}}
            <h2>
                21. Disclaimer of Warranties
            </h2>

            <p>
                To the maximum extent permitted by applicable law, Restrotix
                is provided on an “as available” basis.
            </p>

            <p>
                We do not guarantee that every feature will always be
                available, every error will be eliminated, or use of Restrotix
                will produce any particular business or financial outcome.
            </p>


            {{-- 22 --}}
            <h2>
                22. Limitation of Liability
            </h2>

            <p>
                To the maximum extent permitted by applicable law, Restrotix
                will not be responsible for indirect, incidental, special or
                consequential losses arising from the use of the Services
                where such limitation is legally permitted.
            </p>

            <p>
                Nothing in these Terms excludes rights or liabilities that
                cannot lawfully be excluded.
            </p>


            {{-- 23 --}}
            <h2>
                23. Indemnification
            </h2>

            <p>
                To the extent permitted by law, users are responsible for
                claims or losses resulting from unlawful use of Restrotix,
                violation of these Terms, unlawful customer data, or
                infringement of third-party rights.
            </p>


            {{-- 24 --}}
            <h2>
                24. Changes to the Services
            </h2>

            <p>
                Restrotix may add, modify or discontinue features as
                technology, customer requirements, laws, security requirements
                and business needs evolve.
            </p>


            {{-- 25 --}}
            <h2>
                25. Changes to These Terms
            </h2>

            <p>
                These Terms may be updated from time to time. The latest
                version will display an updated “Last Updated” date.
            </p>


            {{-- 26 --}}
            <h2>
                26. Governing Law and Disputes
            </h2>

            <p>
                These Terms are governed by applicable laws of Nepal.
            </p>

            <p>
                Parties should first attempt to resolve disputes through
                good-faith communication. Unresolved disputes may be submitted
                to a competent court or lawful dispute-resolution mechanism
                in Nepal.
            </p>


            {{-- 27 --}}
            <h2>
                27. Severability
            </h2>

            <p>
                If any provision is found invalid or unenforceable, the
                remaining provisions will continue in effect to the fullest
                extent permitted by law.
            </p>


            {{-- 28 --}}
            <h2>
                28. Entire Agreement
            </h2>

            <p>
                These Terms, our Privacy Policy, applicable subscription
                terms, quotations, order forms, marketplace terms and
                separately signed agreements govern your use of applicable
                Restrotix Services.
            </p>


            {{-- 29 --}}
            <h2>
                29. Contact Us
            </h2>

            <p>
                For questions regarding these Terms or Restrotix Services,
                contact us through the official support or enquiry channels
                available on the Restrotix website.
            </p>

            <p>
                <strong>Website:</strong>

                <a href="{{ url('/') }}">
                    restrotix.com
                </a>
            </p>


            {{-- =================================================
                BOTTOM
            ================================================== --}}
            <div class="restrotix-legal-footer">

                <p class="restrotix-legal-updated">
                  <strong>Last Updated:</strong> September 9, 2026
                </p>

                

            </div>

        </div>

    </div>

</section>

@endsection