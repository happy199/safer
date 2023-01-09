<?php
namespace App\Extensions\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Classe permettant d'utiliser la fonction MATCH AGAINST de MySQL dans une requête Doctrine.
 */

class MatchAgainst extends FunctionNode
{
    /** @var array list of \Doctrine\ORM\Query\AST\PathExpression */
    protected $pathExp = null;
    /** @var string */
    protected $against = null;
    /** @var bool */
    protected $booleanMode = false;
    /** @var bool */
    protected $queryExpansion = false;

    /**
     * Parse la partie de la requête concernée par la fonction MATCH AGAINST.
     *
     */

    public function parse(Parser $parser)
    {
        // match
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        // premier Path Expression est obligatoire
        $this->pathExp = [];
        $this->pathExp[] = $parser->StateFieldPathExpression();
        // les Path Expressions suivants sont optionnels
        $lexer = $parser->getLexer();
        while ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->pathExp[] = $parser->StateFieldPathExpression();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
        // Ceci aussi
        if (strtolower($lexer->lookahead['value']) !== 'against') {
            $parser->syntaxError('against');
        }
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->against = $parser->StringPrimary();
        if (strtolower($lexer->lookahead['value']) === 'boolean') {
            $parser->match(Lexer::T_IDENTIFIER);
            $this->booleanMode = true;
        }
        if (strtolower($lexer->lookahead['value']) === 'expand') {
            $parser->match(Lexer::T_IDENTIFIER);
            $this->queryExpansion = true;
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Génère le code SQL correspondant à la fonction MATCH AGAINST.
     */

    public function getSql(SqlWalker $walker)
    {
        // Initialisation du tableau contenant les champs à passer à la fonction MATCH AGAINST
        $fields = [];
        // Parcours des expressions de chemin de l'entité
        foreach ($this->pathExp as $pathExp) {
            $fields[] = $pathExp->dispatch($walker);
        }
        // Construction de la chaîne contenant les paramètres de la fonction MATCH AGAINST
        $against = $walker->walkStringPrimary($this->against)
            . ($this->booleanMode ? ' IN BOOLEAN MODE' : '')
            . ($this->queryExpansion ? ' WITH QUERY EXPANSION' : '');
        // Retour du code SQL généré pour la fonction MATCH AGAINST
        return sprintf('MATCH (%s) AGAINST (%s)', implode(', ', $fields), $against);
    }
}