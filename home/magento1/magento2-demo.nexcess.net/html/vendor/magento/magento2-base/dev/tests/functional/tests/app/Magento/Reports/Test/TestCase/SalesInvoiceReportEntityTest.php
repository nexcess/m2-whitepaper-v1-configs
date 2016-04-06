<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Reports\Test\Page\Adminhtml\SalesInvoiceReport;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Open Backend
 * 2. Go to Reports > Sales > Invoiced
 * 3. Refresh statistic
 * 4. Configure filter
 * 5. Click "Show Report"
 * 6. Save/remember report result
 * 7. Create customer
 * 8. Place order
 * 9. Create Invoice
 * 10. Refresh statistic
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Reports > Sales > Invoiced
 * 3. Configure filter
 * 4. Click "Show Report"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-29216
 */
class SalesInvoiceReportEntityTest extends Injectable
{
    /* tags */
    const MVP = 'no';
    const DOMAIN = 'MX';
    /* end tags */

    /**
     * Sales invoice report.
     *
     * @param SalesInvoiceReport $salesInvoiceReport
     * @param OrderInjectable $order
     * @param array $invoiceReport
     * @return array
     */
    public function test(SalesInvoiceReport $salesInvoiceReport, OrderInjectable $order, array $invoiceReport)
    {
        // Preconditions
        $salesInvoiceReport->open();
        $salesInvoiceReport->getMessagesBlock()->clickLinkInMessage('notice', 'here');
        $salesInvoiceReport->getFilterForm()->viewsReport($invoiceReport);
        $salesInvoiceReport->getActionBlock()->showReport();
        $initialInvoiceResult = $salesInvoiceReport->getGridBlock()->getLastResult();
        $initialInvoiceTotalResult = $salesInvoiceReport->getGridBlock()->getTotalResult();
        $order->persist();
        $invoice = $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order]);
        $invoice->run();

        return [
            'initialInvoiceResult' => $initialInvoiceResult,
            'initialInvoiceTotalResult' => $initialInvoiceTotalResult
        ];
    }
}
