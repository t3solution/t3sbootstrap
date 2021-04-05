<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class LoopViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('end', 'int', 'Loop until iterator equals');
        $this->registerArgument('start', 'int', 'Start at number', false, 1);
        $this->registerArgument('reverse', 'bool', 'Count reverse', false, false);
        $this->registerArgument('iteration', 'string', 'Variable name for iterator');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     * @throws ViewHelper\Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $templateVariableContainer = $renderingContext->getVariableProvider();

        $output = '';

        if (!$arguments['reverse']) {
            for ($i = $arguments['start']; $i <= $arguments['end']; $i++) {
                if (isset($arguments['iteration'])) {
                    $templateVariableContainer->add($arguments['iteration'], $i);
                }
                $output .= $renderChildrenClosure();
            }
        } else {
            for ($i = $arguments['start']; $i >= $arguments['end']; $i++) {
                if (isset($arguments['iteration'])) {
                    $templateVariableContainer->add($arguments['iteration'], $i);
                }
                $output .= $renderChildrenClosure();
            }
        }

        if (isset($arguments['iteration'])) {
            $templateVariableContainer->remove($arguments['iteration']);
        }

        return $output;
    }
}
