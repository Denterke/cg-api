<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 13/07/15
 * Time: 13:23
 */

namespace Farpost\CatalogueBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Farpost\StoreBundle\Entity\Version;

class VersionBlockService extends BaseBlockService {
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param string                                                     $name
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Versions block';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildEditBlock(FormMapper $formMapper, BlockInterface $block)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        $settings = array_merge($this->getDefaultSettings(), $block->getSettings());
        $versionRepository = $this->em->getRepository('FarpostStoreBundle:Version');
        $versions = $versionRepository->getForWeb();
        $processingCnt = $versionRepository->getProcessingEntitiesCount(Version::CATALOG_V2);

        return $this->renderResponse('FarpostCatalogueBundle:Block:block_versions.html.twig', [
            'block' => $block,
            'settings' => $settings,
            'versions' => $versions,
            'processingCnt' => $processingCnt
        ], $response);
    }
}