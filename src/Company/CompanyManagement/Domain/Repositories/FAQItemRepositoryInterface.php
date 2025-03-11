<?php

namespace Src\Company\CompanyManagement\Domain\Repositories;
use Src\Company\CompanyManagement\Domain\Model\Entities\FAQItem;
use Src\Company\CompanyManagement\Application\DTO\FAQItemData;

interface FAQItemRepositoryInterface
{
    public function getFAQItems($filters = []);

    public function getCustomerFAQ();

    public function replyCustomerFAQ(int $faqId,?string $answer = null);

    public function findFAQItemById(int $id): FAQItemData;

    public function store(FAQItem $faq): FAQItemData;

    public function update(FAQItem $faq): FAQItem;

    public function delete(int $faq_id): void;

}
