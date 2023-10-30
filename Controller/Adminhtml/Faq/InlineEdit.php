<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_Faq
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use MageINIC\Faq\Model\Faq;
use Magento\Backend\App\Action\Context;
use Magento\Cms\Model\Block;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Faq Class InlineEdit
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_Faq::faq_edit';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var Faq
     */
    private Faq $faqModel;

    /**
     * InlineEdit constructor.
     *
     * @param Context       $context
     * @param JsonFactory   $jsonFactory
     * @param Faq           $faqModel
     */
    public function __construct(
        Context     $context,
        JsonFactory $jsonFactory,
        Faq         $faqModel
    ) {
        $this->faqModel     = $faqModel;
        $this->jsonFactory  = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (empty($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    /** @var Block $block */
                    $model = $this->faqModel->load($modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Faq ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        return $resultJson->setData(['messages' => $messages, 'error' => $error]);
    }
}
