<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 05.10.2015
 * Time: 21:11
 */

namespace Angrybender\Pattern;

use \PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    private $line;

    /**
     * @var Node
     */
    private $node = null;

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);
    }

    public function enterNode(Node $node)
    {
        if (empty($this->node) && $this->line == $node->getAttribute('endLine')) {
            $this->node = $node;
        }

        parent::enterNode($node);
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);
    }

    public function afterTraverse(array $nodes)
    {
        parent::afterTraverse($nodes);
    }

    /**
     * @param mixed $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

}