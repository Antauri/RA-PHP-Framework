<?php
/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Provides an hierarchy object to work with MPTT tables (tables that have a right/left field. Google for more info);
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
 */
class Hierarchy extends Database {
    /* Fields; */
    public $objIdField = NULL;
    public $objRightField = NULL;
    public $objLeftyField = NULL;
    public $objNameOfNode = NULL;
    public $objSEOName = NULL;
    public $objNodeDate = NULL;

    /* Privates */
    private $objTable = NULL;
    private $objSilentIgnr = NULL;

    // CONSTANTS
    const FIRST_CHILD = 1;
    const LAST_CHILD = 2;
    const PREVIOUS_BROTHER = 3;
    const NEXT_BROTHER = 4;
    const PADDING = '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;';

    // CONSTRUCT
    public function __construct (S $objTable, S $objRootNodeRL = NULL, B $objSilentIgnr = NULL,
    S $objNameOfNode = NULL, S $objRightField = NULL, S $objLeftyField = NULL,
    S $objIdField = NULL, S $objSEOName = NULL, S $objNodeDate = NULL) {

        // Requirements
        if ($objRightField == NULL) { $objRightField = new S ('rgt');   }
        if ($objLeftyField == NULL) { $objLeftyField = new S ('lft');   }
        if ($objNameOfNode == NULL) { $objNameOfNode = new S ('name');  }
        if ($objSEOName    == NULL) { $objSEOName    = new S ('seo');   }
        if ($objNodeDate   == NULL) { $objNodeDate   = new S ('date');  }
        if ($objRootNodeRL == NULL) { $objRootNodeRL = new S ('/');     }
        if ($objIdField    == NULL) { $objIdField    = new S ('id');    }
        if ($objSilentIgnr == NULL) { $objSilentIgnr = new B (FALSE);   }

        // Requirements
        $this->objTable      = $objTable;
        $this->objIdField    = $objIdField;
        $this->objRightField = $objRightField;
        $this->objLeftyField = $objLeftyField;
        $this->objNameOfNode = $objNameOfNode;
        $this->objSEOName    = $objSEOName;
        $this->objNodeDate   = $objNodeDate;
        $this->objSilentIgnr = $objSilentIgnr;

        // Add the ROOT node;
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', NULL))->doCount ()->toInt () == 0) {
            // Add an SQL condition;
            $objSQLCondition = new S ('%objNameOfNode = "%nId",
            %objLeftyField = "1", %objRightField = "2",
            %objSEOName = "%sId", %objNodeDate = "%dId"');

            // Add it, cause the table's empty;
            $this->_Q (_QS ('doINSERT')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', $objSQLCondition)
            ->doToken ('%nId', $objRootNodeRL)
            ->doToken ('%sId', Location::getFrom ($objRootNodeRL))
            ->doToken ('%dId', time ()));
        }
    }

    /**
     * Changes SQL tokens to ones specific for this object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function doChangeToken (S $objSQLParam) {
        // Tokens
        $objTokens = new A;
        $objTokens[] = 'objTable';
        $objTokens[] = 'objIdField';
        $objTokens[] = 'objRightField';
        $objTokens[] = 'objLeftyField';
        $objTokens[] = 'objNameOfNode';
        $objTokens[] = 'objSEOName';
        $objTokens[] = 'objNodeDate';

        // Strings
        $objReplac = new A;
        $objReplac[] = $this->objTable;
        $objReplac[] = $this->objIdField;
        $objReplac[] = $this->objRightField;
        $objReplac[] = $this->objLeftyField;
        $objReplac[] = $this->objNameOfNode;
        $objReplac[] = $this->objSEOName;
        $objReplac[] = $this->objNodeDate;

        // Do a CALL to your parents;
        return parent::doChangeTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Returns node info by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetNodeById (S $objNodeId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objIdField = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objNodeId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns node info by node name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetNodeByName (S $objNodeName, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objNodeName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Checks if node exists;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttCheckIfNodeExists (S $objCheckedNode) {
        // Check
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCheckedNode))->doCount ()->toInt () != 0) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Adds node in relation to a parent node, either first or last;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttAddNode (S $objANode, S $objPNode, S $objAddLeftRight = NULL) {
        // Set some predefines;
        if ($objAddLeftRight == NULL) { $objAddLeftRight = new S ((string) self::FIRST_CHILD); }

        // Check that the inserted node is unique;
        if ($this->mpttCheckIfNodeExists ($objANode)->toBoolean () == TRUE) {
            if ($this->objSilentIgnr->toBoolean () == FALSE) {
                // Send an error if the node is not unique;
                throw new Exception ('Exception raised!');
            } else {
                // Return FALSE, don't allow further execution;
                return new B (FALSE);
            }
        } else {
            // Do a switch on the type of node;
            switch ((int) (string) $objAddLeftRight) {
                // Add as the first child of;
                case Hierarchy::FIRST_CHILD:
                    $this->mpttNewFirstChild ($objANode, $objPNode);
                    break;

                    // Or the last child of;
                case Hierarchy::LAST_CHILD:
                    $this->mpttNewLastChild ($objANode, $objPNode);
                    break;

                    // We can even do a previous brother;
                case Hierarchy::PREVIOUS_BROTHER:
                    $this->mpttNewPrevSibling ($objANode, $objPNode);
                    break;

                    // Or a next one;
                case Hierarchy::NEXT_BROTHER:
                    $this->mpttNewNextSibling ($objANode, $objPNode);
                    break;
            }

            // Return
            return new B (TRUE);
        }
    }

    /**
     * Moves node in relation to a parrent node, as a type;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttMoveNode (S $objNodeName, S $objNodePName, S $objMoveType) {
        // Switch
        switch ($objMoveType) {
            case '1':
                $this->mpttMoveToFirstChild
                ($objNodeName, $objNodePName);
                break;

            case '2':
                $this->mpttMoveToLastChild
                ($objNodeName, $objNodePName);
                break;

            case '3':
                $this->mpttMoveToPrevSibling
                ($objNodeName, $objNodePName);
                break;

            case '4':
                $this->mpttMoveToNextSibling
                ($objNodeName, $objNodePName);
                break;
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Removes given node, recursive or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttRemoveNode (S $objNodeToRemove, B $objRemoveRecursive = NULL) {
        // Set some predefines;
        if ($objRemoveRecursive == NULL) { $objRemoveRecursive = new B (FALSE); }

        // Determine if the node we want to delete is a LEAF node, or not!
        $objLeafNodes = $this->mpttGetTreeLeafs ();
        $objIsALeafNd = new B (FALSE);

        // Parse the array;
        foreach ($objLeafNodes as $objK => $objV) {
            if ($objV[$this->objNameOfNode] == $objNodeToRemove) {
                $objIsALeafNd->switchType ();
            }
        }

        // Get some node information;
        $objQ = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%nId"'))
        ->doToken ('%nId', $objNodeToRemove));

        // Set left, right and width;
        $objLefty = $objQ->offsetGet (0)->offsetGet ($this->objLeftyField);
        $objRight = $objQ->offsetGet (0)->offsetGet ($this->objRightField);
        $objWidth = new S ((string) ((int) (string) $objRight - (int) (string) $objLefty + 1));

        // Check if we do a recursive delete, or promotion delete;
        if (($objIsALeafNd->toBoolean () == TRUE) || ($objRemoveRecursive->toBoolean () == TRUE)) {
            // Do the node deletion;
            $this->_Q (_QS ('doDELETE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField BETWEEN %LowerLimit AND %UpperLimit'))
            ->doToken ('%LowerLimit', $objLefty)->doToken ('%UpperLimit', $objRight));

            // Update the right-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField = %objRightField - %LimitWidth
            WHERE %objRightField > %UpperLimit'))
            ->doToken ('%LimitWidth', $objWidth)->doToken ('%UpperLimit', $objRight));

            // Update the lefty-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField - %LimitWidth
            WHERE %objLeftyField > %UpperLimit'))
            ->doToken ('%LimitWidth', $objWidth)->doToken ('%UpperLimit', $objRight));

            // Return
            return new B (TRUE);
        } else {
            // Do the node deletion;
            $this->_Q (_QS ('doDELETE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField = %LowerLimit'))
            ->doToken ('%LowerLimit', $objLefty));

            // If we removed, promote kids;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField =
            %objRightField - 1, %objLeftyField = %objLeftyField - 1
            WHERE %objLeftyField BETWEEN %LowerLimit AND %UpperLimit'))
            ->doToken ('%LowerLimit', $objLefty)->doToken ('%UpperLimit', $objRight));

            // Update the right-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField =
            %objRightField - 2 WHERE %objRightField > %UpperLimit'))
            ->doToken ('%UpperLimit', $objRight));

            // Update the lefty-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField =
            %objLeftyField - 2 WHERE %objLeftyField > %UpperLimit'))
            ->doToken ('%UpperLimit', $objRight));

            // Return
            return new B (TRUE);
        }
    }

    /**
     * Returns a tree from the given subnode, if any;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetTree (S $objSubNode = NULL, S $objSQLConditionOrder = NULL) {
        // Check
        if ($objSubNode == NULL) {
            // Set some predefines;
            if ($objSQLConditionOrder == NULL) $objSQLConditionOrder = new S ('ASC');

            // Do a BIG condition;
            $objSQLCondition = new S;
            $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField %condition');

            // Return
            return $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', new S ('n.id, n.%objNameOfNode, (COUNT(p.%objNameOfNode) - 1) AS depth'))
            ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)
            ->doToken ('%condition', $objSQLConditionOrder));
        } else {
            // Do a BIG condition;
            $objSQLCondition = new S;
            $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p, %table AS s,');
            $objSQLCondition->appendString (_SP)->appendString ('(SELECT n.%objNameOfNode,(COUNT(p.%objNameOfNode) - 1) AS depth');
            $objSQLCondition->appendString (_SP)->appendString ('FROM %table AS n, %table AS p');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField) AS t');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN s.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND s.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND s.%objNameOfNode = t.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField');

            // Return
            return $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', new S ('n.%objIdField, n.%objNameOfNode, (COUNT(p.%objNameOfNode) - (t.depth + 1)) AS depth'))
            ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)->doToken ('%nId', $objSubNode));
        }
    }

    /**
     * Returns the node single path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetSinglePath (S $objNodeFrom) {
        // Do a BIG condition;
        $objSQLCondition = new S;
        $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY p.%objLeftyField');

        // Return the path to the root;
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('p.%objNameOfNode'))->doToken ('%condition', $objSQLCondition)
        ->doToken ('%table', $this->objTable)->doToken ('%nId', $objNodeFrom));
    }

    /**
     * Returns the tree depth;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetTreeDepth (S $objNode, S $objDepth = NULL) {
        // Set some predefined defaults;
        if ($objDepth == NULL) { $objDepth = new S ('1'); }

        // Do a BIG condition;
        $objSQLCondition = new S;
        $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p, %table AS s,');
        $objSQLCondition->appendString (_SP)->appendString ('(SELECT n.%objNameOfNode, (COUNT(p.%objNameOfNode) - 1) AS depth');
        $objSQLCondition->appendString (_SP)->appendString ('FROM %table AS n, %table AS p');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
        $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField) AS t');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN s.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND s.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND s.%objNameOfNode = t.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('HAVING depth <= %dId');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField');

        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('n.%objNameOfNode, (COUNT(p.%objNameOfNode) - (t.depth + 1)) AS depth'))
        ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)
        ->doToken ('%dId', $objDepth)->doToken ('%nId', $objNode));
    }

    /**
     * Returns tree leafs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public function mpttGetTreeLeafs () {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $this->objNameOfNode)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objLeftyField = %objRightField - 1')));
    }

    /**
     * Removes the unique identifier of given node;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function mpttRemoveUnique (S $objNodeName) {
        // Return
        return $objNodeName
        ->pregChange ('/-uniQ-[0-9]*/', _NONE);
    }

    /**
     * Adds the unique identifier to the given node name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function mpttAddUnique (S $objNodeName, S $objNodeTimestamp) {
        if ($objNodeName->findIPos ('-uniQ-') instanceof B) {
            // Return
            return $objNodeName
            ->appendString ('-uniQ-%Id')
            ->doToken ('%Id', $objNodeTimestamp);
        } else {
            // Nada
            return $objNodeName;
        }
    }

    /**
     * Adds the node as a new child of the parent;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttNewFirstChild (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objLeftyField) + 1));

        $objPRight = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objLeftyField) + 2));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', Location::getFrom ($objNodeName))
        ->doToken ('%dId', time ()));
    }

    /**
     * Adds the node as a new child of the parent, last;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttNewLastChild (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objRightField) + 0));

        $objPRight = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objRightField) + 1));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', Location::getFrom ($objNodeName))
        ->doToken ('%dId', time ()));
    }

    /**
     * Adds the new node as the previous sibling of the parent node;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttNewPrevSibling (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objLeftyField) + 0));

        $objPRight = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objLeftyField) + 1));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', Location::getFrom ($objNodeName))
        ->doToken ('%dId', time ()));
    }

    /**
     * Adds the new node as the next sibling of the parent node;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttNewNextSibling (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objRightField) + 1));

        $objPRight = new S ((string) ((int) (string) $this
        ->mpttGetNodeByName ($objNodePName, $this->objRightField) + 2));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', Location::getFrom ($objNodeName))
        ->doToken ('%dId', time ()));
    }

    /**
     * Moves sub-tree as previous sibling;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttMoveToPrevSibling (S $objNodeName, S $objNodePName) {
        return $this->moveSubTree ($objNodeName, $this
        ->mpttGetNodeByName ($objNodePName, $this->objLeftyField));
    }

    /**
     * Moves sub-tree as last child;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttMoveToLastChild (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, $this
        ->mpttGetNodeByName ($objNodePName, $this->objRightField));
    }

 	/**
 	 * Moves sub-tree as next-sibling;
 	 *
 	 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttMoveToNextSibling (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, new S ((string) ((int) (string)
        $this->mpttGetNodeByName ($objNodePName, $this->objRightField) + 1)));
    }

    /**
     * Moves sub-tree as first child;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function mpttMoveToFirstChild (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, new S ((string) ((int) (string)
        $this->mpttGetNodeByName ($objNodePName, $this->objLeftyField) + 1)));
    }

    /**
     * Shifts all right indexes;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function shiftRL (S $objFirst, S $objDelta) {
        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField + %dId
        WHERE %objLeftyField >= %fId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst));

        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objRightField = %objRightField + %dId
        WHERE %objRightField >= %fId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst));
    }

    /**
     * Shifts all left indexes via range;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function shiftRLRange (S $objFirst, S $objLast, S $objDelta) {
        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField + %dId WHERE %objLeftyField >= %fId
        AND %objLeftyField <= %eId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst)->doToken ('%eId', $objLast));

        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objRightField = %objRightField + %dId WHERE %objRightField >= %fId
        AND %objRightField <= %eId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst)->doToken ('%eId', $objLast));
    }

    /**
     * Moves a sub-tree from a node to another;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hierarchy.php 1 2012-10-26 08:27:37Z root $
     */
    private function moveSubTree (S $objNodeName, S $objNodeTo) {
        // Get some info from them;
        $objNodeNamePLefty = $this->mpttGetNodeByName ($objNodeName, $this->objLeftyField);
        $objNodeNamePRight = $this->mpttGetNodeByName ($objNodeName, $this->objRightField);

        // Get the tree size;
        $objTreeSize = new S ((string) ((int) (string)
        $objNodeNamePRight - (int) (string) $objNodeNamePLefty + 1));

        // Shifting;
        $this->shiftRL ($objNodeTo, $objTreeSize);

        // If;
        if ((int) (string) $objNodeNamePLefty > (int) (string) $objNodeTo) {
            $objNodeNamePLefty = new S ((string) ((int) (string)
            $objNodeNamePLefty + (int) (string) $objTreeSize));

            $objNodeNamePRight = new S ((string) ((int) (string)
            $objNodeNamePRight + (int) (string) $objTreeSize));
        }

        // Shifting;
        $this->shiftRLRange ($objNodeNamePLefty, $objNodeNamePRight,
        $objDelta = new S ((string) ((int) (string) $objNodeTo - (int) (string) $objNodeNamePLefty)));
        $this->shiftRL (new S ((string) ((int) (string) $objNodeNamePRight + 1)),
        new S ((string) (-1 * (int) (string) $objTreeSize)));
    }
}
?>