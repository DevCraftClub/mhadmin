<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/main.html */
class __TwigTemplate_9ed5a26db3b8c536a49ee3ede08d73192a08c3847e0e40ea91174b74dc0007f6 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("base.html", "modules/main.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    public function block_content($context, array $blocks = [])
    {
        // line 2
        echo "<div class=\"ui segment\">
\t<form class=\"ui form\">
\t\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Name</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Billing Address</label>
\t\t\t<div class=\"fields\">
\t\t\t\t<div class=\"twelve wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"four wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<label>State</label>
\t\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t\t<option value=\"\">State</option>
\t\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<label>Country</label>
\t\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t\t<input type=\"hidden\" name=\"country\" />
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t<div class=\"item\" data-value=\"af\">
\t\t\t\t\t\t\t<i class=\"af flag\"></i>Afghanistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ax\">
\t\t\t\t\t\t\t<i class=\"ax flag\"></i>Aland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"al\">
\t\t\t\t\t\t\t<i class=\"al flag\"></i>Albania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dz\">
\t\t\t\t\t\t\t<i class=\"dz flag\"></i>Algeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"as\">
\t\t\t\t\t\t\t<i class=\"as flag\"></i>American Samoa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ad\">
\t\t\t\t\t\t\t<i class=\"ad flag\"></i>Andorra
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ao\">
\t\t\t\t\t\t\t<i class=\"ao flag\"></i>Angola
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ai\">
\t\t\t\t\t\t\t<i class=\"ai flag\"></i>Anguilla
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ag\">
\t\t\t\t\t\t\t<i class=\"ag flag\"></i>Antigua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ar\">
\t\t\t\t\t\t\t<i class=\"ar flag\"></i>Argentina
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"am\">
\t\t\t\t\t\t\t<i class=\"am flag\"></i>Armenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"au\">
\t\t\t\t\t\t\t<i class=\"au flag\"></i>Australia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"at\">
\t\t\t\t\t\t\t<i class=\"at flag\"></i>Austria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"az\">
\t\t\t\t\t\t\t<i class=\"az flag\"></i>Azerbaijan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bs\">
\t\t\t\t\t\t\t<i class=\"bs flag\"></i>Bahamas
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bh\">
\t\t\t\t\t\t\t<i class=\"bh flag\"></i>Bahrain
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bd\">
\t\t\t\t\t\t\t<i class=\"bd flag\"></i>Bangladesh
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bb\">
\t\t\t\t\t\t\t<i class=\"bb flag\"></i>Barbados
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"by\">
\t\t\t\t\t\t\t<i class=\"by flag\"></i>Belarus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"be\">
\t\t\t\t\t\t\t<i class=\"be flag\"></i>Belgium
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bz\">
\t\t\t\t\t\t\t<i class=\"bz flag\"></i>Belize
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bm\">
\t\t\t\t\t\t\t<i class=\"bm flag\"></i>Bermuda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bt\">
\t\t\t\t\t\t\t<i class=\"bt flag\"></i>Bhutan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bo\">
\t\t\t\t\t\t\t<i class=\"bo flag\"></i>Bolivia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ba\">
\t\t\t\t\t\t\t<i class=\"ba flag\"></i>Bosnia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bw\">
\t\t\t\t\t\t\t<i class=\"bw flag\"></i>Botswana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bv\">
\t\t\t\t\t\t\t<i class=\"bv flag\"></i>Bouvet Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"br\">
\t\t\t\t\t\t\t<i class=\"br flag\"></i>Brazil
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vg\">
\t\t\t\t\t\t\t<i class=\"vg flag\"></i>British Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bn\">
\t\t\t\t\t\t\t<i class=\"bn flag\"></i>Brunei
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bg\">
\t\t\t\t\t\t\t<i class=\"bg flag\"></i>Bulgaria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bf\">
\t\t\t\t\t\t\t<i class=\"bf flag\"></i>Burkina Faso
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bi\">
\t\t\t\t\t\t\t<i class=\"bi flag\"></i>Burundi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tc\">
\t\t\t\t\t\t\t<i class=\"tc flag\"></i>Caicos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kh\">
\t\t\t\t\t\t\t<i class=\"kh flag\"></i>Cambodia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cm\">
\t\t\t\t\t\t\t<i class=\"cm flag\"></i>Cameroon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ca\">
\t\t\t\t\t\t\t<i class=\"ca flag\"></i>Canada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cv\">
\t\t\t\t\t\t\t<i class=\"cv flag\"></i>Cape Verde
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ky\">
\t\t\t\t\t\t\t<i class=\"ky flag\"></i>Cayman Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cf\">
\t\t\t\t\t\t\t<i class=\"cf flag\"></i>Central African Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cx\">
\t\t\t\t\t\t\t<i class=\"cx flag\"></i>Christmas Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cc\">
\t\t\t\t\t\t\t<i class=\"cc flag\"></i>Cocos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"co\">
\t\t\t\t\t\t\t<i class=\"co flag\"></i>Colombia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"km\">
\t\t\t\t\t\t\t<i class=\"km flag\"></i>Comoros
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cg\">
\t\t\t\t\t\t\t<i class=\"cg flag\"></i>Congo Brazzaville
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ck\">
\t\t\t\t\t\t\t<i class=\"ck flag\"></i>Cook Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cr\">
\t\t\t\t\t\t\t<i class=\"cr flag\"></i>Costa Rica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ci\">
\t\t\t\t\t\t\t<i class=\"ci flag\"></i>Cote Divoire
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hr\">
\t\t\t\t\t\t\t<i class=\"hr flag\"></i>Croatia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cy\">
\t\t\t\t\t\t\t<i class=\"cy flag\"></i>Cyprus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cz\">
\t\t\t\t\t\t\t<i class=\"cz flag\"></i>Czech Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dk\">
\t\t\t\t\t\t\t<i class=\"dk flag\"></i>Denmark
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dj\">
\t\t\t\t\t\t\t<i class=\"dj flag\"></i>Djibouti
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dm\">
\t\t\t\t\t\t\t<i class=\"dm flag\"></i>Dominica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"do\">
\t\t\t\t\t\t\t<i class=\"do flag\"></i>Dominican Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ec\">
\t\t\t\t\t\t\t<i class=\"ec flag\"></i>Ecuador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sv\">
\t\t\t\t\t\t\t<i class=\"sv flag\"></i>El Salvador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gb\">
\t\t\t\t\t\t\t<i class=\"gb flag\"></i>England
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gq\">
\t\t\t\t\t\t\t<i class=\"gq flag\"></i>Equatorial Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"er\">
\t\t\t\t\t\t\t<i class=\"er flag\"></i>Eritrea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ee\">
\t\t\t\t\t\t\t<i class=\"ee flag\"></i>Estonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"et\">
\t\t\t\t\t\t\t<i class=\"et flag\"></i>Ethiopia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eu\">
\t\t\t\t\t\t\t<i class=\"eu flag\"></i>European Union
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fk\">
\t\t\t\t\t\t\t<i class=\"fk flag\"></i>Falkland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fo\">
\t\t\t\t\t\t\t<i class=\"fo flag\"></i>Faroe Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fi\">
\t\t\t\t\t\t\t<i class=\"fi flag\"></i>Finland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fr\">
\t\t\t\t\t\t\t<i class=\"fr flag\"></i>France
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gf\">
\t\t\t\t\t\t\t<i class=\"gf flag\"></i>French Guiana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pf\">
\t\t\t\t\t\t\t<i class=\"pf flag\"></i>French Polynesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tf\">
\t\t\t\t\t\t\t<i class=\"tf flag\"></i>French Territories
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gm\">
\t\t\t\t\t\t\t<i class=\"gm flag\"></i>Gambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ge\">
\t\t\t\t\t\t\t<i class=\"ge flag\"></i>Georgia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"de\">
\t\t\t\t\t\t\t<i class=\"de flag\"></i>Germany
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gi\">
\t\t\t\t\t\t\t<i class=\"gi flag\"></i>Gibraltar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gr\">
\t\t\t\t\t\t\t<i class=\"gr flag\"></i>Greece
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gl\">
\t\t\t\t\t\t\t<i class=\"gl flag\"></i>Greenland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gd\">
\t\t\t\t\t\t\t<i class=\"gd flag\"></i>Grenada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gp\">
\t\t\t\t\t\t\t<i class=\"gp flag\"></i>Guadeloupe
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gt\">
\t\t\t\t\t\t\t<i class=\"gt flag\"></i>Guatemala
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gw\">
\t\t\t\t\t\t\t<i class=\"gw flag\"></i>Guinea-Bissau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gn\">
\t\t\t\t\t\t\t<i class=\"gn flag\"></i>Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gy\">
\t\t\t\t\t\t\t<i class=\"gy flag\"></i>Guyana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hm\">
\t\t\t\t\t\t\t<i class=\"hm flag\"></i>Heard Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hn\">
\t\t\t\t\t\t\t<i class=\"hn flag\"></i>Honduras
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hk\">
\t\t\t\t\t\t\t<i class=\"hk flag\"></i>Hong Kong
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hu\">
\t\t\t\t\t\t\t<i class=\"hu flag\"></i>Hungary
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"is\">
\t\t\t\t\t\t\t<i class=\"is flag\"></i>Iceland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"io\">
\t\t\t\t\t\t\t<i class=\"io flag\"></i>Indian Ocean Territory
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"id\">
\t\t\t\t\t\t\t<i class=\"id flag\"></i>Indonesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ie\">
\t\t\t\t\t\t\t<i class=\"ie flag\"></i>Ireland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"il\">
\t\t\t\t\t\t\t<i class=\"il flag\"></i>Israel
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jm\">
\t\t\t\t\t\t\t<i class=\"jm flag\"></i>Jamaica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jo\">
\t\t\t\t\t\t\t<i class=\"jo flag\"></i>Jordan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kz\">
\t\t\t\t\t\t\t<i class=\"kz flag\"></i>Kazakhstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ki\">
\t\t\t\t\t\t\t<i class=\"ki flag\"></i>Kiribati
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kw\">
\t\t\t\t\t\t\t<i class=\"kw flag\"></i>Kuwait
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kg\">
\t\t\t\t\t\t\t<i class=\"kg flag\"></i>Kyrgyzstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lv\">
\t\t\t\t\t\t\t<i class=\"lv flag\"></i>Latvia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lb\">
\t\t\t\t\t\t\t<i class=\"lb flag\"></i>Lebanon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ls\">
\t\t\t\t\t\t\t<i class=\"ls flag\"></i>Lesotho
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lr\">
\t\t\t\t\t\t\t<i class=\"lr flag\"></i>Liberia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"li\">
\t\t\t\t\t\t\t<i class=\"li flag\"></i>Liechtenstein
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lt\">
\t\t\t\t\t\t\t<i class=\"lt flag\"></i>Lithuania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lu\">
\t\t\t\t\t\t\t<i class=\"lu flag\"></i>Luxembourg
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mk\">
\t\t\t\t\t\t\t<i class=\"mk flag\"></i>Macedonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mg\">
\t\t\t\t\t\t\t<i class=\"mg flag\"></i>Madagascar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mw\">
\t\t\t\t\t\t\t<i class=\"mw flag\"></i>Malawi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"my\">
\t\t\t\t\t\t\t<i class=\"my flag\"></i>Malaysia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mv\">
\t\t\t\t\t\t\t<i class=\"mv flag\"></i>Maldives
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mh\">
\t\t\t\t\t\t\t<i class=\"mh flag\"></i>Marshall Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mq\">
\t\t\t\t\t\t\t<i class=\"mq flag\"></i>Martinique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mr\">
\t\t\t\t\t\t\t<i class=\"mr flag\"></i>Mauritania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mu\">
\t\t\t\t\t\t\t<i class=\"mu flag\"></i>Mauritius
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"yt\">
\t\t\t\t\t\t\t<i class=\"yt flag\"></i>Mayotte
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mx\">
\t\t\t\t\t\t\t<i class=\"mx flag\"></i>Mexico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fm\">
\t\t\t\t\t\t\t<i class=\"fm flag\"></i>Micronesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"md\">
\t\t\t\t\t\t\t<i class=\"md flag\"></i>Moldova
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mc\">
\t\t\t\t\t\t\t<i class=\"mc flag\"></i>Monaco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mn\">
\t\t\t\t\t\t\t<i class=\"mn flag\"></i>Mongolia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"me\">
\t\t\t\t\t\t\t<i class=\"me flag\"></i>Montenegro
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ms\">
\t\t\t\t\t\t\t<i class=\"ms flag\"></i>Montserrat
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ma\">
\t\t\t\t\t\t\t<i class=\"ma flag\"></i>Morocco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mz\">
\t\t\t\t\t\t\t<i class=\"mz flag\"></i>Mozambique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"na\">
\t\t\t\t\t\t\t<i class=\"na flag\"></i>Namibia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"an\">
\t\t\t\t\t\t\t<i class=\"an flag\"></i>Netherlands Antilles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nl\">
\t\t\t\t\t\t\t<i class=\"nl flag\"></i>Netherlands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nc\">
\t\t\t\t\t\t\t<i class=\"nc flag\"></i>New Caledonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pg\">
\t\t\t\t\t\t\t<i class=\"pg flag\"></i>New Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nz\">
\t\t\t\t\t\t\t<i class=\"nz flag\"></i>New Zealand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ni\">
\t\t\t\t\t\t\t<i class=\"ni flag\"></i>Nicaragua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ng\">
\t\t\t\t\t\t\t<i class=\"ng flag\"></i>Nigeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nf\">
\t\t\t\t\t\t\t<i class=\"nf flag\"></i>Norfolk Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kp\">
\t\t\t\t\t\t\t<i class=\"kp flag\"></i>North Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mp\">
\t\t\t\t\t\t\t<i class=\"mp flag\"></i>Northern Mariana Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"no\">
\t\t\t\t\t\t\t<i class=\"no flag\"></i>Norway
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pk\">
\t\t\t\t\t\t\t<i class=\"pk flag\"></i>Pakistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ps\">
\t\t\t\t\t\t\t<i class=\"ps flag\"></i>Palestine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pa\">
\t\t\t\t\t\t\t<i class=\"pa flag\"></i>Panama
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"py\">
\t\t\t\t\t\t\t<i class=\"py flag\"></i>Paraguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ph\">
\t\t\t\t\t\t\t<i class=\"ph flag\"></i>Philippines
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pn\">
\t\t\t\t\t\t\t<i class=\"pn flag\"></i>Pitcairn Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pl\">
\t\t\t\t\t\t\t<i class=\"pl flag\"></i>Poland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pt\">
\t\t\t\t\t\t\t<i class=\"pt flag\"></i>Portugal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pr\">
\t\t\t\t\t\t\t<i class=\"pr flag\"></i>Puerto Rico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"re\">
\t\t\t\t\t\t\t<i class=\"re flag\"></i>Reunion
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ro\">
\t\t\t\t\t\t\t<i class=\"ro flag\"></i>Romania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ru\">
\t\t\t\t\t\t\t<i class=\"ru flag\"></i>Russia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rw\">
\t\t\t\t\t\t\t<i class=\"rw flag\"></i>Rwanda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sh\">
\t\t\t\t\t\t\t<i class=\"sh flag\"></i>Saint Helena
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kn\">
\t\t\t\t\t\t\t<i class=\"kn flag\"></i>Saint Kitts and Nevis
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lc\">
\t\t\t\t\t\t\t<i class=\"lc flag\"></i>Saint Lucia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pm\">
\t\t\t\t\t\t\t<i class=\"pm flag\"></i>Saint Pierre
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vc\">
\t\t\t\t\t\t\t<i class=\"vc flag\"></i>Saint Vincent
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sm\">
\t\t\t\t\t\t\t<i class=\"sm flag\"></i>San Marino
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gs\">
\t\t\t\t\t\t\t<i class=\"gs flag\"></i>Sandwich Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"st\">
\t\t\t\t\t\t\t<i class=\"st flag\"></i>Sao Tome
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sa\">
\t\t\t\t\t\t\t<i class=\"sa flag\"></i>Saudi Arabia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sn\">
\t\t\t\t\t\t\t<i class=\"sn flag\"></i>Senegal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cs\">
\t\t\t\t\t\t\t<i class=\"cs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rs\">
\t\t\t\t\t\t\t<i class=\"rs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sc\">
\t\t\t\t\t\t\t<i class=\"sc flag\"></i>Seychelles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sl\">
\t\t\t\t\t\t\t<i class=\"sl flag\"></i>Sierra Leone
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sg\">
\t\t\t\t\t\t\t<i class=\"sg flag\"></i>Singapore
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sk\">
\t\t\t\t\t\t\t<i class=\"sk flag\"></i>Slovakia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"si\">
\t\t\t\t\t\t\t<i class=\"si flag\"></i>Slovenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sb\">
\t\t\t\t\t\t\t<i class=\"sb flag\"></i>Solomon Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"so\">
\t\t\t\t\t\t\t<i class=\"so flag\"></i>Somalia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"za\">
\t\t\t\t\t\t\t<i class=\"za flag\"></i>South Africa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kr\">
\t\t\t\t\t\t\t<i class=\"kr flag\"></i>South Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lk\">
\t\t\t\t\t\t\t<i class=\"lk flag\"></i>Sri Lanka
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sr\">
\t\t\t\t\t\t\t<i class=\"sr flag\"></i>Suriname
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sj\">
\t\t\t\t\t\t\t<i class=\"sj flag\"></i>Svalbard
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sz\">
\t\t\t\t\t\t\t<i class=\"sz flag\"></i>Swaziland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"se\">
\t\t\t\t\t\t\t<i class=\"se flag\"></i>Sweden
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ch\">
\t\t\t\t\t\t\t<i class=\"ch flag\"></i>Switzerland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tw\">
\t\t\t\t\t\t\t<i class=\"tw flag\"></i>Taiwan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tj\">
\t\t\t\t\t\t\t<i class=\"tj flag\"></i>Tajikistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tz\">
\t\t\t\t\t\t\t<i class=\"tz flag\"></i>Tanzania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"th\">
\t\t\t\t\t\t\t<i class=\"th flag\"></i>Thailand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tl\">
\t\t\t\t\t\t\t<i class=\"tl flag\"></i>Timorleste
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tk\">
\t\t\t\t\t\t\t<i class=\"tk flag\"></i>Tokelau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tt\">
\t\t\t\t\t\t\t<i class=\"tt flag\"></i>Trinidad
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tn\">
\t\t\t\t\t\t\t<i class=\"tn flag\"></i>Tunisia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tr\">
\t\t\t\t\t\t\t<i class=\"tr flag\"></i>Turkey
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tm\">
\t\t\t\t\t\t\t<i class=\"tm flag\"></i>Turkmenistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tv\">
\t\t\t\t\t\t\t<i class=\"tv flag\"></i>Tuvalu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ug\">
\t\t\t\t\t\t\t<i class=\"ug flag\"></i>Uganda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ua\">
\t\t\t\t\t\t\t<i class=\"ua flag\"></i>Ukraine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ae\">
\t\t\t\t\t\t\t<i class=\"ae flag\"></i>United Arab Emirates
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"us\">
\t\t\t\t\t\t\t<i class=\"us flag\"></i>United States
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uy\">
\t\t\t\t\t\t\t<i class=\"uy flag\"></i>Uruguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"um\">
\t\t\t\t\t\t\t<i class=\"um flag\"></i>Us Minor Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vi\">
\t\t\t\t\t\t\t<i class=\"vi flag\"></i>Us Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uz\">
\t\t\t\t\t\t\t<i class=\"uz flag\"></i>Uzbekistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vu\">
\t\t\t\t\t\t\t<i class=\"vu flag\"></i>Vanuatu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"va\">
\t\t\t\t\t\t\t<i class=\"va flag\"></i>Vatican City
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ve\">
\t\t\t\t\t\t\t<i class=\"ve flag\"></i>Venezuela
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vn\">
\t\t\t\t\t\t\t<i class=\"vn flag\"></i>Vietnam
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"wf\">
\t\t\t\t\t\t\t<i class=\"wf flag\"></i>Wallis and Futuna
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eh\">
\t\t\t\t\t\t\t<i class=\"eh flag\"></i>Western Sahara
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zm\">
\t\t\t\t\t\t\t<i class=\"zm flag\"></i>Zambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zw\">
\t\t\t\t\t\t\t<i class=\"zw flag\"></i>Zimbabwe
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Card Type</label>
\t\t\t<div class=\"ui selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"card[type]\" />
\t\t\t\t<div class=\"default text\">Type</div>
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\t\tVisa
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\t\tAmerican Express
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\t\tDiscover
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"fields\">
\t\t\t<div class=\"seven wide field\">
\t\t\t\t<label>Card Number</label>
\t\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\" />
\t\t\t</div>
\t\t\t<div class=\"three wide field\">
\t\t\t\t<label>CVC</label>
\t\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\" />
\t\t\t</div>
\t\t\t<div class=\"six wide field\">
\t\t\t\t<label>Expiration</label>
\t\t\t\t<div class=\"two fields\">
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Receipt</h4>
\t\t<div class=\"field\">
\t\t\t<label>Send Receipt To:</label>
\t\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"receipt\" />
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\" />
\t\t\t\t\t\tJenny Hess
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\" />
\t\t\t\t\t\tElliot Fu
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\" />
\t\t\t\t\t\tStevie Feliciano
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\" />
\t\t\t\t\t\tChristian
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\" />
\t\t\t\t\t\tMatt
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\" />
\t\t\t\t\t\tJusten Kitsune
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui segment\">
\t\t\t<div class=\"field\">
\t\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\" />
\t\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
\t</form>
</div>

<div class=\"ui segment\">
\t<form class=\"ui form\">
\t\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Name</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Billing Address</label>
\t\t\t<div class=\"fields\">
\t\t\t\t<div class=\"twelve wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"four wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<label>State</label>
\t\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t\t<option value=\"\">State</option>
\t\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<label>Country</label>
\t\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t\t<input type=\"hidden\" name=\"country\" />
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t<div class=\"item\" data-value=\"af\">
\t\t\t\t\t\t\t<i class=\"af flag\"></i>Afghanistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ax\">
\t\t\t\t\t\t\t<i class=\"ax flag\"></i>Aland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"al\">
\t\t\t\t\t\t\t<i class=\"al flag\"></i>Albania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dz\">
\t\t\t\t\t\t\t<i class=\"dz flag\"></i>Algeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"as\">
\t\t\t\t\t\t\t<i class=\"as flag\"></i>American Samoa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ad\">
\t\t\t\t\t\t\t<i class=\"ad flag\"></i>Andorra
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ao\">
\t\t\t\t\t\t\t<i class=\"ao flag\"></i>Angola
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ai\">
\t\t\t\t\t\t\t<i class=\"ai flag\"></i>Anguilla
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ag\">
\t\t\t\t\t\t\t<i class=\"ag flag\"></i>Antigua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ar\">
\t\t\t\t\t\t\t<i class=\"ar flag\"></i>Argentina
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"am\">
\t\t\t\t\t\t\t<i class=\"am flag\"></i>Armenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"au\">
\t\t\t\t\t\t\t<i class=\"au flag\"></i>Australia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"at\">
\t\t\t\t\t\t\t<i class=\"at flag\"></i>Austria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"az\">
\t\t\t\t\t\t\t<i class=\"az flag\"></i>Azerbaijan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bs\">
\t\t\t\t\t\t\t<i class=\"bs flag\"></i>Bahamas
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bh\">
\t\t\t\t\t\t\t<i class=\"bh flag\"></i>Bahrain
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bd\">
\t\t\t\t\t\t\t<i class=\"bd flag\"></i>Bangladesh
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bb\">
\t\t\t\t\t\t\t<i class=\"bb flag\"></i>Barbados
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"by\">
\t\t\t\t\t\t\t<i class=\"by flag\"></i>Belarus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"be\">
\t\t\t\t\t\t\t<i class=\"be flag\"></i>Belgium
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bz\">
\t\t\t\t\t\t\t<i class=\"bz flag\"></i>Belize
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bm\">
\t\t\t\t\t\t\t<i class=\"bm flag\"></i>Bermuda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bt\">
\t\t\t\t\t\t\t<i class=\"bt flag\"></i>Bhutan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bo\">
\t\t\t\t\t\t\t<i class=\"bo flag\"></i>Bolivia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ba\">
\t\t\t\t\t\t\t<i class=\"ba flag\"></i>Bosnia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bw\">
\t\t\t\t\t\t\t<i class=\"bw flag\"></i>Botswana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bv\">
\t\t\t\t\t\t\t<i class=\"bv flag\"></i>Bouvet Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"br\">
\t\t\t\t\t\t\t<i class=\"br flag\"></i>Brazil
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vg\">
\t\t\t\t\t\t\t<i class=\"vg flag\"></i>British Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bn\">
\t\t\t\t\t\t\t<i class=\"bn flag\"></i>Brunei
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bg\">
\t\t\t\t\t\t\t<i class=\"bg flag\"></i>Bulgaria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bf\">
\t\t\t\t\t\t\t<i class=\"bf flag\"></i>Burkina Faso
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bi\">
\t\t\t\t\t\t\t<i class=\"bi flag\"></i>Burundi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tc\">
\t\t\t\t\t\t\t<i class=\"tc flag\"></i>Caicos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kh\">
\t\t\t\t\t\t\t<i class=\"kh flag\"></i>Cambodia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cm\">
\t\t\t\t\t\t\t<i class=\"cm flag\"></i>Cameroon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ca\">
\t\t\t\t\t\t\t<i class=\"ca flag\"></i>Canada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cv\">
\t\t\t\t\t\t\t<i class=\"cv flag\"></i>Cape Verde
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ky\">
\t\t\t\t\t\t\t<i class=\"ky flag\"></i>Cayman Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cf\">
\t\t\t\t\t\t\t<i class=\"cf flag\"></i>Central African Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cx\">
\t\t\t\t\t\t\t<i class=\"cx flag\"></i>Christmas Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cc\">
\t\t\t\t\t\t\t<i class=\"cc flag\"></i>Cocos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"co\">
\t\t\t\t\t\t\t<i class=\"co flag\"></i>Colombia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"km\">
\t\t\t\t\t\t\t<i class=\"km flag\"></i>Comoros
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cg\">
\t\t\t\t\t\t\t<i class=\"cg flag\"></i>Congo Brazzaville
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ck\">
\t\t\t\t\t\t\t<i class=\"ck flag\"></i>Cook Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cr\">
\t\t\t\t\t\t\t<i class=\"cr flag\"></i>Costa Rica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ci\">
\t\t\t\t\t\t\t<i class=\"ci flag\"></i>Cote Divoire
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hr\">
\t\t\t\t\t\t\t<i class=\"hr flag\"></i>Croatia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cy\">
\t\t\t\t\t\t\t<i class=\"cy flag\"></i>Cyprus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cz\">
\t\t\t\t\t\t\t<i class=\"cz flag\"></i>Czech Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dk\">
\t\t\t\t\t\t\t<i class=\"dk flag\"></i>Denmark
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dj\">
\t\t\t\t\t\t\t<i class=\"dj flag\"></i>Djibouti
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dm\">
\t\t\t\t\t\t\t<i class=\"dm flag\"></i>Dominica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"do\">
\t\t\t\t\t\t\t<i class=\"do flag\"></i>Dominican Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ec\">
\t\t\t\t\t\t\t<i class=\"ec flag\"></i>Ecuador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sv\">
\t\t\t\t\t\t\t<i class=\"sv flag\"></i>El Salvador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gb\">
\t\t\t\t\t\t\t<i class=\"gb flag\"></i>England
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gq\">
\t\t\t\t\t\t\t<i class=\"gq flag\"></i>Equatorial Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"er\">
\t\t\t\t\t\t\t<i class=\"er flag\"></i>Eritrea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ee\">
\t\t\t\t\t\t\t<i class=\"ee flag\"></i>Estonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"et\">
\t\t\t\t\t\t\t<i class=\"et flag\"></i>Ethiopia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eu\">
\t\t\t\t\t\t\t<i class=\"eu flag\"></i>European Union
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fk\">
\t\t\t\t\t\t\t<i class=\"fk flag\"></i>Falkland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fo\">
\t\t\t\t\t\t\t<i class=\"fo flag\"></i>Faroe Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fi\">
\t\t\t\t\t\t\t<i class=\"fi flag\"></i>Finland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fr\">
\t\t\t\t\t\t\t<i class=\"fr flag\"></i>France
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gf\">
\t\t\t\t\t\t\t<i class=\"gf flag\"></i>French Guiana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pf\">
\t\t\t\t\t\t\t<i class=\"pf flag\"></i>French Polynesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tf\">
\t\t\t\t\t\t\t<i class=\"tf flag\"></i>French Territories
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gm\">
\t\t\t\t\t\t\t<i class=\"gm flag\"></i>Gambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ge\">
\t\t\t\t\t\t\t<i class=\"ge flag\"></i>Georgia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"de\">
\t\t\t\t\t\t\t<i class=\"de flag\"></i>Germany
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gi\">
\t\t\t\t\t\t\t<i class=\"gi flag\"></i>Gibraltar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gr\">
\t\t\t\t\t\t\t<i class=\"gr flag\"></i>Greece
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gl\">
\t\t\t\t\t\t\t<i class=\"gl flag\"></i>Greenland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gd\">
\t\t\t\t\t\t\t<i class=\"gd flag\"></i>Grenada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gp\">
\t\t\t\t\t\t\t<i class=\"gp flag\"></i>Guadeloupe
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gt\">
\t\t\t\t\t\t\t<i class=\"gt flag\"></i>Guatemala
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gw\">
\t\t\t\t\t\t\t<i class=\"gw flag\"></i>Guinea-Bissau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gn\">
\t\t\t\t\t\t\t<i class=\"gn flag\"></i>Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gy\">
\t\t\t\t\t\t\t<i class=\"gy flag\"></i>Guyana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hm\">
\t\t\t\t\t\t\t<i class=\"hm flag\"></i>Heard Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hn\">
\t\t\t\t\t\t\t<i class=\"hn flag\"></i>Honduras
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hk\">
\t\t\t\t\t\t\t<i class=\"hk flag\"></i>Hong Kong
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hu\">
\t\t\t\t\t\t\t<i class=\"hu flag\"></i>Hungary
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"is\">
\t\t\t\t\t\t\t<i class=\"is flag\"></i>Iceland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"io\">
\t\t\t\t\t\t\t<i class=\"io flag\"></i>Indian Ocean Territory
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"id\">
\t\t\t\t\t\t\t<i class=\"id flag\"></i>Indonesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ie\">
\t\t\t\t\t\t\t<i class=\"ie flag\"></i>Ireland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"il\">
\t\t\t\t\t\t\t<i class=\"il flag\"></i>Israel
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jm\">
\t\t\t\t\t\t\t<i class=\"jm flag\"></i>Jamaica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jo\">
\t\t\t\t\t\t\t<i class=\"jo flag\"></i>Jordan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kz\">
\t\t\t\t\t\t\t<i class=\"kz flag\"></i>Kazakhstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ki\">
\t\t\t\t\t\t\t<i class=\"ki flag\"></i>Kiribati
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kw\">
\t\t\t\t\t\t\t<i class=\"kw flag\"></i>Kuwait
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kg\">
\t\t\t\t\t\t\t<i class=\"kg flag\"></i>Kyrgyzstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lv\">
\t\t\t\t\t\t\t<i class=\"lv flag\"></i>Latvia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lb\">
\t\t\t\t\t\t\t<i class=\"lb flag\"></i>Lebanon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ls\">
\t\t\t\t\t\t\t<i class=\"ls flag\"></i>Lesotho
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lr\">
\t\t\t\t\t\t\t<i class=\"lr flag\"></i>Liberia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"li\">
\t\t\t\t\t\t\t<i class=\"li flag\"></i>Liechtenstein
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lt\">
\t\t\t\t\t\t\t<i class=\"lt flag\"></i>Lithuania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lu\">
\t\t\t\t\t\t\t<i class=\"lu flag\"></i>Luxembourg
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mk\">
\t\t\t\t\t\t\t<i class=\"mk flag\"></i>Macedonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mg\">
\t\t\t\t\t\t\t<i class=\"mg flag\"></i>Madagascar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mw\">
\t\t\t\t\t\t\t<i class=\"mw flag\"></i>Malawi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"my\">
\t\t\t\t\t\t\t<i class=\"my flag\"></i>Malaysia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mv\">
\t\t\t\t\t\t\t<i class=\"mv flag\"></i>Maldives
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mh\">
\t\t\t\t\t\t\t<i class=\"mh flag\"></i>Marshall Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mq\">
\t\t\t\t\t\t\t<i class=\"mq flag\"></i>Martinique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mr\">
\t\t\t\t\t\t\t<i class=\"mr flag\"></i>Mauritania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mu\">
\t\t\t\t\t\t\t<i class=\"mu flag\"></i>Mauritius
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"yt\">
\t\t\t\t\t\t\t<i class=\"yt flag\"></i>Mayotte
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mx\">
\t\t\t\t\t\t\t<i class=\"mx flag\"></i>Mexico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fm\">
\t\t\t\t\t\t\t<i class=\"fm flag\"></i>Micronesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"md\">
\t\t\t\t\t\t\t<i class=\"md flag\"></i>Moldova
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mc\">
\t\t\t\t\t\t\t<i class=\"mc flag\"></i>Monaco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mn\">
\t\t\t\t\t\t\t<i class=\"mn flag\"></i>Mongolia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"me\">
\t\t\t\t\t\t\t<i class=\"me flag\"></i>Montenegro
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ms\">
\t\t\t\t\t\t\t<i class=\"ms flag\"></i>Montserrat
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ma\">
\t\t\t\t\t\t\t<i class=\"ma flag\"></i>Morocco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mz\">
\t\t\t\t\t\t\t<i class=\"mz flag\"></i>Mozambique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"na\">
\t\t\t\t\t\t\t<i class=\"na flag\"></i>Namibia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"an\">
\t\t\t\t\t\t\t<i class=\"an flag\"></i>Netherlands Antilles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nl\">
\t\t\t\t\t\t\t<i class=\"nl flag\"></i>Netherlands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nc\">
\t\t\t\t\t\t\t<i class=\"nc flag\"></i>New Caledonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pg\">
\t\t\t\t\t\t\t<i class=\"pg flag\"></i>New Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nz\">
\t\t\t\t\t\t\t<i class=\"nz flag\"></i>New Zealand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ni\">
\t\t\t\t\t\t\t<i class=\"ni flag\"></i>Nicaragua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ng\">
\t\t\t\t\t\t\t<i class=\"ng flag\"></i>Nigeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nf\">
\t\t\t\t\t\t\t<i class=\"nf flag\"></i>Norfolk Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kp\">
\t\t\t\t\t\t\t<i class=\"kp flag\"></i>North Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mp\">
\t\t\t\t\t\t\t<i class=\"mp flag\"></i>Northern Mariana Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"no\">
\t\t\t\t\t\t\t<i class=\"no flag\"></i>Norway
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pk\">
\t\t\t\t\t\t\t<i class=\"pk flag\"></i>Pakistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ps\">
\t\t\t\t\t\t\t<i class=\"ps flag\"></i>Palestine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pa\">
\t\t\t\t\t\t\t<i class=\"pa flag\"></i>Panama
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"py\">
\t\t\t\t\t\t\t<i class=\"py flag\"></i>Paraguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ph\">
\t\t\t\t\t\t\t<i class=\"ph flag\"></i>Philippines
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pn\">
\t\t\t\t\t\t\t<i class=\"pn flag\"></i>Pitcairn Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pl\">
\t\t\t\t\t\t\t<i class=\"pl flag\"></i>Poland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pt\">
\t\t\t\t\t\t\t<i class=\"pt flag\"></i>Portugal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pr\">
\t\t\t\t\t\t\t<i class=\"pr flag\"></i>Puerto Rico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"re\">
\t\t\t\t\t\t\t<i class=\"re flag\"></i>Reunion
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ro\">
\t\t\t\t\t\t\t<i class=\"ro flag\"></i>Romania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ru\">
\t\t\t\t\t\t\t<i class=\"ru flag\"></i>Russia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rw\">
\t\t\t\t\t\t\t<i class=\"rw flag\"></i>Rwanda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sh\">
\t\t\t\t\t\t\t<i class=\"sh flag\"></i>Saint Helena
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kn\">
\t\t\t\t\t\t\t<i class=\"kn flag\"></i>Saint Kitts and Nevis
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lc\">
\t\t\t\t\t\t\t<i class=\"lc flag\"></i>Saint Lucia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pm\">
\t\t\t\t\t\t\t<i class=\"pm flag\"></i>Saint Pierre
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vc\">
\t\t\t\t\t\t\t<i class=\"vc flag\"></i>Saint Vincent
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sm\">
\t\t\t\t\t\t\t<i class=\"sm flag\"></i>San Marino
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gs\">
\t\t\t\t\t\t\t<i class=\"gs flag\"></i>Sandwich Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"st\">
\t\t\t\t\t\t\t<i class=\"st flag\"></i>Sao Tome
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sa\">
\t\t\t\t\t\t\t<i class=\"sa flag\"></i>Saudi Arabia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sn\">
\t\t\t\t\t\t\t<i class=\"sn flag\"></i>Senegal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cs\">
\t\t\t\t\t\t\t<i class=\"cs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rs\">
\t\t\t\t\t\t\t<i class=\"rs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sc\">
\t\t\t\t\t\t\t<i class=\"sc flag\"></i>Seychelles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sl\">
\t\t\t\t\t\t\t<i class=\"sl flag\"></i>Sierra Leone
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sg\">
\t\t\t\t\t\t\t<i class=\"sg flag\"></i>Singapore
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sk\">
\t\t\t\t\t\t\t<i class=\"sk flag\"></i>Slovakia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"si\">
\t\t\t\t\t\t\t<i class=\"si flag\"></i>Slovenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sb\">
\t\t\t\t\t\t\t<i class=\"sb flag\"></i>Solomon Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"so\">
\t\t\t\t\t\t\t<i class=\"so flag\"></i>Somalia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"za\">
\t\t\t\t\t\t\t<i class=\"za flag\"></i>South Africa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kr\">
\t\t\t\t\t\t\t<i class=\"kr flag\"></i>South Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lk\">
\t\t\t\t\t\t\t<i class=\"lk flag\"></i>Sri Lanka
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sr\">
\t\t\t\t\t\t\t<i class=\"sr flag\"></i>Suriname
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sj\">
\t\t\t\t\t\t\t<i class=\"sj flag\"></i>Svalbard
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sz\">
\t\t\t\t\t\t\t<i class=\"sz flag\"></i>Swaziland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"se\">
\t\t\t\t\t\t\t<i class=\"se flag\"></i>Sweden
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ch\">
\t\t\t\t\t\t\t<i class=\"ch flag\"></i>Switzerland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tw\">
\t\t\t\t\t\t\t<i class=\"tw flag\"></i>Taiwan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tj\">
\t\t\t\t\t\t\t<i class=\"tj flag\"></i>Tajikistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tz\">
\t\t\t\t\t\t\t<i class=\"tz flag\"></i>Tanzania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"th\">
\t\t\t\t\t\t\t<i class=\"th flag\"></i>Thailand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tl\">
\t\t\t\t\t\t\t<i class=\"tl flag\"></i>Timorleste
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tk\">
\t\t\t\t\t\t\t<i class=\"tk flag\"></i>Tokelau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tt\">
\t\t\t\t\t\t\t<i class=\"tt flag\"></i>Trinidad
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tn\">
\t\t\t\t\t\t\t<i class=\"tn flag\"></i>Tunisia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tr\">
\t\t\t\t\t\t\t<i class=\"tr flag\"></i>Turkey
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tm\">
\t\t\t\t\t\t\t<i class=\"tm flag\"></i>Turkmenistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tv\">
\t\t\t\t\t\t\t<i class=\"tv flag\"></i>Tuvalu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ug\">
\t\t\t\t\t\t\t<i class=\"ug flag\"></i>Uganda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ua\">
\t\t\t\t\t\t\t<i class=\"ua flag\"></i>Ukraine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ae\">
\t\t\t\t\t\t\t<i class=\"ae flag\"></i>United Arab Emirates
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"us\">
\t\t\t\t\t\t\t<i class=\"us flag\"></i>United States
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uy\">
\t\t\t\t\t\t\t<i class=\"uy flag\"></i>Uruguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"um\">
\t\t\t\t\t\t\t<i class=\"um flag\"></i>Us Minor Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vi\">
\t\t\t\t\t\t\t<i class=\"vi flag\"></i>Us Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uz\">
\t\t\t\t\t\t\t<i class=\"uz flag\"></i>Uzbekistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vu\">
\t\t\t\t\t\t\t<i class=\"vu flag\"></i>Vanuatu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"va\">
\t\t\t\t\t\t\t<i class=\"va flag\"></i>Vatican City
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ve\">
\t\t\t\t\t\t\t<i class=\"ve flag\"></i>Venezuela
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vn\">
\t\t\t\t\t\t\t<i class=\"vn flag\"></i>Vietnam
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"wf\">
\t\t\t\t\t\t\t<i class=\"wf flag\"></i>Wallis and Futuna
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eh\">
\t\t\t\t\t\t\t<i class=\"eh flag\"></i>Western Sahara
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zm\">
\t\t\t\t\t\t\t<i class=\"zm flag\"></i>Zambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zw\">
\t\t\t\t\t\t\t<i class=\"zw flag\"></i>Zimbabwe
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Card Type</label>
\t\t\t<div class=\"ui selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"card[type]\" />
\t\t\t\t<div class=\"default text\">Type</div>
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\t\tVisa
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\t\tAmerican Express
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\t\tDiscover
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"fields\">
\t\t\t<div class=\"seven wide field\">
\t\t\t\t<label>Card Number</label>
\t\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\" />
\t\t\t</div>
\t\t\t<div class=\"three wide field\">
\t\t\t\t<label>CVC</label>
\t\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\" />
\t\t\t</div>
\t\t\t<div class=\"six wide field\">
\t\t\t\t<label>Expiration</label>
\t\t\t\t<div class=\"two fields\">
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Receipt</h4>
\t\t<div class=\"field\">
\t\t\t<label>Send Receipt To:</label>
\t\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"receipt\" />
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\" />
\t\t\t\t\t\tJenny Hess
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\" />
\t\t\t\t\t\tElliot Fu
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\" />
\t\t\t\t\t\tStevie Feliciano
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\" />
\t\t\t\t\t\tChristian
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\" />
\t\t\t\t\t\tMatt
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\" />
\t\t\t\t\t\tJusten Kitsune
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui segment\">
\t\t\t<div class=\"field\">
\t\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\" />
\t\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
\t</form>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/main.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 2,  29 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html' %} {% block content %}
<div class=\"ui segment\">
\t<form class=\"ui form\">
\t\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Name</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Billing Address</label>
\t\t\t<div class=\"fields\">
\t\t\t\t<div class=\"twelve wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"four wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<label>State</label>
\t\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t\t<option value=\"\">State</option>
\t\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<label>Country</label>
\t\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t\t<input type=\"hidden\" name=\"country\" />
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t<div class=\"item\" data-value=\"af\">
\t\t\t\t\t\t\t<i class=\"af flag\"></i>Afghanistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ax\">
\t\t\t\t\t\t\t<i class=\"ax flag\"></i>Aland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"al\">
\t\t\t\t\t\t\t<i class=\"al flag\"></i>Albania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dz\">
\t\t\t\t\t\t\t<i class=\"dz flag\"></i>Algeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"as\">
\t\t\t\t\t\t\t<i class=\"as flag\"></i>American Samoa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ad\">
\t\t\t\t\t\t\t<i class=\"ad flag\"></i>Andorra
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ao\">
\t\t\t\t\t\t\t<i class=\"ao flag\"></i>Angola
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ai\">
\t\t\t\t\t\t\t<i class=\"ai flag\"></i>Anguilla
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ag\">
\t\t\t\t\t\t\t<i class=\"ag flag\"></i>Antigua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ar\">
\t\t\t\t\t\t\t<i class=\"ar flag\"></i>Argentina
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"am\">
\t\t\t\t\t\t\t<i class=\"am flag\"></i>Armenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"au\">
\t\t\t\t\t\t\t<i class=\"au flag\"></i>Australia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"at\">
\t\t\t\t\t\t\t<i class=\"at flag\"></i>Austria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"az\">
\t\t\t\t\t\t\t<i class=\"az flag\"></i>Azerbaijan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bs\">
\t\t\t\t\t\t\t<i class=\"bs flag\"></i>Bahamas
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bh\">
\t\t\t\t\t\t\t<i class=\"bh flag\"></i>Bahrain
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bd\">
\t\t\t\t\t\t\t<i class=\"bd flag\"></i>Bangladesh
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bb\">
\t\t\t\t\t\t\t<i class=\"bb flag\"></i>Barbados
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"by\">
\t\t\t\t\t\t\t<i class=\"by flag\"></i>Belarus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"be\">
\t\t\t\t\t\t\t<i class=\"be flag\"></i>Belgium
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bz\">
\t\t\t\t\t\t\t<i class=\"bz flag\"></i>Belize
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bm\">
\t\t\t\t\t\t\t<i class=\"bm flag\"></i>Bermuda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bt\">
\t\t\t\t\t\t\t<i class=\"bt flag\"></i>Bhutan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bo\">
\t\t\t\t\t\t\t<i class=\"bo flag\"></i>Bolivia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ba\">
\t\t\t\t\t\t\t<i class=\"ba flag\"></i>Bosnia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bw\">
\t\t\t\t\t\t\t<i class=\"bw flag\"></i>Botswana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bv\">
\t\t\t\t\t\t\t<i class=\"bv flag\"></i>Bouvet Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"br\">
\t\t\t\t\t\t\t<i class=\"br flag\"></i>Brazil
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vg\">
\t\t\t\t\t\t\t<i class=\"vg flag\"></i>British Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bn\">
\t\t\t\t\t\t\t<i class=\"bn flag\"></i>Brunei
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bg\">
\t\t\t\t\t\t\t<i class=\"bg flag\"></i>Bulgaria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bf\">
\t\t\t\t\t\t\t<i class=\"bf flag\"></i>Burkina Faso
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bi\">
\t\t\t\t\t\t\t<i class=\"bi flag\"></i>Burundi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tc\">
\t\t\t\t\t\t\t<i class=\"tc flag\"></i>Caicos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kh\">
\t\t\t\t\t\t\t<i class=\"kh flag\"></i>Cambodia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cm\">
\t\t\t\t\t\t\t<i class=\"cm flag\"></i>Cameroon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ca\">
\t\t\t\t\t\t\t<i class=\"ca flag\"></i>Canada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cv\">
\t\t\t\t\t\t\t<i class=\"cv flag\"></i>Cape Verde
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ky\">
\t\t\t\t\t\t\t<i class=\"ky flag\"></i>Cayman Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cf\">
\t\t\t\t\t\t\t<i class=\"cf flag\"></i>Central African Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cx\">
\t\t\t\t\t\t\t<i class=\"cx flag\"></i>Christmas Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cc\">
\t\t\t\t\t\t\t<i class=\"cc flag\"></i>Cocos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"co\">
\t\t\t\t\t\t\t<i class=\"co flag\"></i>Colombia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"km\">
\t\t\t\t\t\t\t<i class=\"km flag\"></i>Comoros
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cg\">
\t\t\t\t\t\t\t<i class=\"cg flag\"></i>Congo Brazzaville
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ck\">
\t\t\t\t\t\t\t<i class=\"ck flag\"></i>Cook Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cr\">
\t\t\t\t\t\t\t<i class=\"cr flag\"></i>Costa Rica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ci\">
\t\t\t\t\t\t\t<i class=\"ci flag\"></i>Cote Divoire
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hr\">
\t\t\t\t\t\t\t<i class=\"hr flag\"></i>Croatia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cy\">
\t\t\t\t\t\t\t<i class=\"cy flag\"></i>Cyprus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cz\">
\t\t\t\t\t\t\t<i class=\"cz flag\"></i>Czech Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dk\">
\t\t\t\t\t\t\t<i class=\"dk flag\"></i>Denmark
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dj\">
\t\t\t\t\t\t\t<i class=\"dj flag\"></i>Djibouti
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dm\">
\t\t\t\t\t\t\t<i class=\"dm flag\"></i>Dominica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"do\">
\t\t\t\t\t\t\t<i class=\"do flag\"></i>Dominican Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ec\">
\t\t\t\t\t\t\t<i class=\"ec flag\"></i>Ecuador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sv\">
\t\t\t\t\t\t\t<i class=\"sv flag\"></i>El Salvador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gb\">
\t\t\t\t\t\t\t<i class=\"gb flag\"></i>England
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gq\">
\t\t\t\t\t\t\t<i class=\"gq flag\"></i>Equatorial Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"er\">
\t\t\t\t\t\t\t<i class=\"er flag\"></i>Eritrea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ee\">
\t\t\t\t\t\t\t<i class=\"ee flag\"></i>Estonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"et\">
\t\t\t\t\t\t\t<i class=\"et flag\"></i>Ethiopia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eu\">
\t\t\t\t\t\t\t<i class=\"eu flag\"></i>European Union
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fk\">
\t\t\t\t\t\t\t<i class=\"fk flag\"></i>Falkland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fo\">
\t\t\t\t\t\t\t<i class=\"fo flag\"></i>Faroe Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fi\">
\t\t\t\t\t\t\t<i class=\"fi flag\"></i>Finland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fr\">
\t\t\t\t\t\t\t<i class=\"fr flag\"></i>France
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gf\">
\t\t\t\t\t\t\t<i class=\"gf flag\"></i>French Guiana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pf\">
\t\t\t\t\t\t\t<i class=\"pf flag\"></i>French Polynesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tf\">
\t\t\t\t\t\t\t<i class=\"tf flag\"></i>French Territories
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gm\">
\t\t\t\t\t\t\t<i class=\"gm flag\"></i>Gambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ge\">
\t\t\t\t\t\t\t<i class=\"ge flag\"></i>Georgia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"de\">
\t\t\t\t\t\t\t<i class=\"de flag\"></i>Germany
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gi\">
\t\t\t\t\t\t\t<i class=\"gi flag\"></i>Gibraltar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gr\">
\t\t\t\t\t\t\t<i class=\"gr flag\"></i>Greece
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gl\">
\t\t\t\t\t\t\t<i class=\"gl flag\"></i>Greenland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gd\">
\t\t\t\t\t\t\t<i class=\"gd flag\"></i>Grenada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gp\">
\t\t\t\t\t\t\t<i class=\"gp flag\"></i>Guadeloupe
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gt\">
\t\t\t\t\t\t\t<i class=\"gt flag\"></i>Guatemala
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gw\">
\t\t\t\t\t\t\t<i class=\"gw flag\"></i>Guinea-Bissau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gn\">
\t\t\t\t\t\t\t<i class=\"gn flag\"></i>Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gy\">
\t\t\t\t\t\t\t<i class=\"gy flag\"></i>Guyana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hm\">
\t\t\t\t\t\t\t<i class=\"hm flag\"></i>Heard Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hn\">
\t\t\t\t\t\t\t<i class=\"hn flag\"></i>Honduras
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hk\">
\t\t\t\t\t\t\t<i class=\"hk flag\"></i>Hong Kong
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hu\">
\t\t\t\t\t\t\t<i class=\"hu flag\"></i>Hungary
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"is\">
\t\t\t\t\t\t\t<i class=\"is flag\"></i>Iceland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"io\">
\t\t\t\t\t\t\t<i class=\"io flag\"></i>Indian Ocean Territory
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"id\">
\t\t\t\t\t\t\t<i class=\"id flag\"></i>Indonesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ie\">
\t\t\t\t\t\t\t<i class=\"ie flag\"></i>Ireland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"il\">
\t\t\t\t\t\t\t<i class=\"il flag\"></i>Israel
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jm\">
\t\t\t\t\t\t\t<i class=\"jm flag\"></i>Jamaica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jo\">
\t\t\t\t\t\t\t<i class=\"jo flag\"></i>Jordan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kz\">
\t\t\t\t\t\t\t<i class=\"kz flag\"></i>Kazakhstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ki\">
\t\t\t\t\t\t\t<i class=\"ki flag\"></i>Kiribati
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kw\">
\t\t\t\t\t\t\t<i class=\"kw flag\"></i>Kuwait
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kg\">
\t\t\t\t\t\t\t<i class=\"kg flag\"></i>Kyrgyzstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lv\">
\t\t\t\t\t\t\t<i class=\"lv flag\"></i>Latvia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lb\">
\t\t\t\t\t\t\t<i class=\"lb flag\"></i>Lebanon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ls\">
\t\t\t\t\t\t\t<i class=\"ls flag\"></i>Lesotho
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lr\">
\t\t\t\t\t\t\t<i class=\"lr flag\"></i>Liberia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"li\">
\t\t\t\t\t\t\t<i class=\"li flag\"></i>Liechtenstein
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lt\">
\t\t\t\t\t\t\t<i class=\"lt flag\"></i>Lithuania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lu\">
\t\t\t\t\t\t\t<i class=\"lu flag\"></i>Luxembourg
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mk\">
\t\t\t\t\t\t\t<i class=\"mk flag\"></i>Macedonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mg\">
\t\t\t\t\t\t\t<i class=\"mg flag\"></i>Madagascar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mw\">
\t\t\t\t\t\t\t<i class=\"mw flag\"></i>Malawi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"my\">
\t\t\t\t\t\t\t<i class=\"my flag\"></i>Malaysia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mv\">
\t\t\t\t\t\t\t<i class=\"mv flag\"></i>Maldives
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mh\">
\t\t\t\t\t\t\t<i class=\"mh flag\"></i>Marshall Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mq\">
\t\t\t\t\t\t\t<i class=\"mq flag\"></i>Martinique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mr\">
\t\t\t\t\t\t\t<i class=\"mr flag\"></i>Mauritania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mu\">
\t\t\t\t\t\t\t<i class=\"mu flag\"></i>Mauritius
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"yt\">
\t\t\t\t\t\t\t<i class=\"yt flag\"></i>Mayotte
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mx\">
\t\t\t\t\t\t\t<i class=\"mx flag\"></i>Mexico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fm\">
\t\t\t\t\t\t\t<i class=\"fm flag\"></i>Micronesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"md\">
\t\t\t\t\t\t\t<i class=\"md flag\"></i>Moldova
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mc\">
\t\t\t\t\t\t\t<i class=\"mc flag\"></i>Monaco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mn\">
\t\t\t\t\t\t\t<i class=\"mn flag\"></i>Mongolia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"me\">
\t\t\t\t\t\t\t<i class=\"me flag\"></i>Montenegro
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ms\">
\t\t\t\t\t\t\t<i class=\"ms flag\"></i>Montserrat
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ma\">
\t\t\t\t\t\t\t<i class=\"ma flag\"></i>Morocco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mz\">
\t\t\t\t\t\t\t<i class=\"mz flag\"></i>Mozambique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"na\">
\t\t\t\t\t\t\t<i class=\"na flag\"></i>Namibia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"an\">
\t\t\t\t\t\t\t<i class=\"an flag\"></i>Netherlands Antilles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nl\">
\t\t\t\t\t\t\t<i class=\"nl flag\"></i>Netherlands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nc\">
\t\t\t\t\t\t\t<i class=\"nc flag\"></i>New Caledonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pg\">
\t\t\t\t\t\t\t<i class=\"pg flag\"></i>New Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nz\">
\t\t\t\t\t\t\t<i class=\"nz flag\"></i>New Zealand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ni\">
\t\t\t\t\t\t\t<i class=\"ni flag\"></i>Nicaragua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ng\">
\t\t\t\t\t\t\t<i class=\"ng flag\"></i>Nigeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nf\">
\t\t\t\t\t\t\t<i class=\"nf flag\"></i>Norfolk Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kp\">
\t\t\t\t\t\t\t<i class=\"kp flag\"></i>North Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mp\">
\t\t\t\t\t\t\t<i class=\"mp flag\"></i>Northern Mariana Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"no\">
\t\t\t\t\t\t\t<i class=\"no flag\"></i>Norway
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pk\">
\t\t\t\t\t\t\t<i class=\"pk flag\"></i>Pakistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ps\">
\t\t\t\t\t\t\t<i class=\"ps flag\"></i>Palestine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pa\">
\t\t\t\t\t\t\t<i class=\"pa flag\"></i>Panama
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"py\">
\t\t\t\t\t\t\t<i class=\"py flag\"></i>Paraguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ph\">
\t\t\t\t\t\t\t<i class=\"ph flag\"></i>Philippines
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pn\">
\t\t\t\t\t\t\t<i class=\"pn flag\"></i>Pitcairn Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pl\">
\t\t\t\t\t\t\t<i class=\"pl flag\"></i>Poland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pt\">
\t\t\t\t\t\t\t<i class=\"pt flag\"></i>Portugal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pr\">
\t\t\t\t\t\t\t<i class=\"pr flag\"></i>Puerto Rico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"re\">
\t\t\t\t\t\t\t<i class=\"re flag\"></i>Reunion
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ro\">
\t\t\t\t\t\t\t<i class=\"ro flag\"></i>Romania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ru\">
\t\t\t\t\t\t\t<i class=\"ru flag\"></i>Russia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rw\">
\t\t\t\t\t\t\t<i class=\"rw flag\"></i>Rwanda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sh\">
\t\t\t\t\t\t\t<i class=\"sh flag\"></i>Saint Helena
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kn\">
\t\t\t\t\t\t\t<i class=\"kn flag\"></i>Saint Kitts and Nevis
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lc\">
\t\t\t\t\t\t\t<i class=\"lc flag\"></i>Saint Lucia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pm\">
\t\t\t\t\t\t\t<i class=\"pm flag\"></i>Saint Pierre
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vc\">
\t\t\t\t\t\t\t<i class=\"vc flag\"></i>Saint Vincent
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sm\">
\t\t\t\t\t\t\t<i class=\"sm flag\"></i>San Marino
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gs\">
\t\t\t\t\t\t\t<i class=\"gs flag\"></i>Sandwich Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"st\">
\t\t\t\t\t\t\t<i class=\"st flag\"></i>Sao Tome
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sa\">
\t\t\t\t\t\t\t<i class=\"sa flag\"></i>Saudi Arabia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sn\">
\t\t\t\t\t\t\t<i class=\"sn flag\"></i>Senegal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cs\">
\t\t\t\t\t\t\t<i class=\"cs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rs\">
\t\t\t\t\t\t\t<i class=\"rs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sc\">
\t\t\t\t\t\t\t<i class=\"sc flag\"></i>Seychelles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sl\">
\t\t\t\t\t\t\t<i class=\"sl flag\"></i>Sierra Leone
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sg\">
\t\t\t\t\t\t\t<i class=\"sg flag\"></i>Singapore
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sk\">
\t\t\t\t\t\t\t<i class=\"sk flag\"></i>Slovakia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"si\">
\t\t\t\t\t\t\t<i class=\"si flag\"></i>Slovenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sb\">
\t\t\t\t\t\t\t<i class=\"sb flag\"></i>Solomon Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"so\">
\t\t\t\t\t\t\t<i class=\"so flag\"></i>Somalia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"za\">
\t\t\t\t\t\t\t<i class=\"za flag\"></i>South Africa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kr\">
\t\t\t\t\t\t\t<i class=\"kr flag\"></i>South Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lk\">
\t\t\t\t\t\t\t<i class=\"lk flag\"></i>Sri Lanka
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sr\">
\t\t\t\t\t\t\t<i class=\"sr flag\"></i>Suriname
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sj\">
\t\t\t\t\t\t\t<i class=\"sj flag\"></i>Svalbard
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sz\">
\t\t\t\t\t\t\t<i class=\"sz flag\"></i>Swaziland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"se\">
\t\t\t\t\t\t\t<i class=\"se flag\"></i>Sweden
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ch\">
\t\t\t\t\t\t\t<i class=\"ch flag\"></i>Switzerland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tw\">
\t\t\t\t\t\t\t<i class=\"tw flag\"></i>Taiwan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tj\">
\t\t\t\t\t\t\t<i class=\"tj flag\"></i>Tajikistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tz\">
\t\t\t\t\t\t\t<i class=\"tz flag\"></i>Tanzania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"th\">
\t\t\t\t\t\t\t<i class=\"th flag\"></i>Thailand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tl\">
\t\t\t\t\t\t\t<i class=\"tl flag\"></i>Timorleste
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tk\">
\t\t\t\t\t\t\t<i class=\"tk flag\"></i>Tokelau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tt\">
\t\t\t\t\t\t\t<i class=\"tt flag\"></i>Trinidad
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tn\">
\t\t\t\t\t\t\t<i class=\"tn flag\"></i>Tunisia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tr\">
\t\t\t\t\t\t\t<i class=\"tr flag\"></i>Turkey
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tm\">
\t\t\t\t\t\t\t<i class=\"tm flag\"></i>Turkmenistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tv\">
\t\t\t\t\t\t\t<i class=\"tv flag\"></i>Tuvalu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ug\">
\t\t\t\t\t\t\t<i class=\"ug flag\"></i>Uganda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ua\">
\t\t\t\t\t\t\t<i class=\"ua flag\"></i>Ukraine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ae\">
\t\t\t\t\t\t\t<i class=\"ae flag\"></i>United Arab Emirates
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"us\">
\t\t\t\t\t\t\t<i class=\"us flag\"></i>United States
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uy\">
\t\t\t\t\t\t\t<i class=\"uy flag\"></i>Uruguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"um\">
\t\t\t\t\t\t\t<i class=\"um flag\"></i>Us Minor Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vi\">
\t\t\t\t\t\t\t<i class=\"vi flag\"></i>Us Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uz\">
\t\t\t\t\t\t\t<i class=\"uz flag\"></i>Uzbekistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vu\">
\t\t\t\t\t\t\t<i class=\"vu flag\"></i>Vanuatu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"va\">
\t\t\t\t\t\t\t<i class=\"va flag\"></i>Vatican City
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ve\">
\t\t\t\t\t\t\t<i class=\"ve flag\"></i>Venezuela
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vn\">
\t\t\t\t\t\t\t<i class=\"vn flag\"></i>Vietnam
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"wf\">
\t\t\t\t\t\t\t<i class=\"wf flag\"></i>Wallis and Futuna
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eh\">
\t\t\t\t\t\t\t<i class=\"eh flag\"></i>Western Sahara
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zm\">
\t\t\t\t\t\t\t<i class=\"zm flag\"></i>Zambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zw\">
\t\t\t\t\t\t\t<i class=\"zw flag\"></i>Zimbabwe
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Card Type</label>
\t\t\t<div class=\"ui selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"card[type]\" />
\t\t\t\t<div class=\"default text\">Type</div>
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\t\tVisa
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\t\tAmerican Express
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\t\tDiscover
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"fields\">
\t\t\t<div class=\"seven wide field\">
\t\t\t\t<label>Card Number</label>
\t\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\" />
\t\t\t</div>
\t\t\t<div class=\"three wide field\">
\t\t\t\t<label>CVC</label>
\t\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\" />
\t\t\t</div>
\t\t\t<div class=\"six wide field\">
\t\t\t\t<label>Expiration</label>
\t\t\t\t<div class=\"two fields\">
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Receipt</h4>
\t\t<div class=\"field\">
\t\t\t<label>Send Receipt To:</label>
\t\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"receipt\" />
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\" />
\t\t\t\t\t\tJenny Hess
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\" />
\t\t\t\t\t\tElliot Fu
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\" />
\t\t\t\t\t\tStevie Feliciano
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\" />
\t\t\t\t\t\tChristian
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\" />
\t\t\t\t\t\tMatt
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\" />
\t\t\t\t\t\tJusten Kitsune
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui segment\">
\t\t\t<div class=\"field\">
\t\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\" />
\t\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
\t</form>
</div>

<div class=\"ui segment\">
\t<form class=\"ui form\">
\t\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Name</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Billing Address</label>
\t\t\t<div class=\"fields\">
\t\t\t\t<div class=\"twelve wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\" />
\t\t\t\t</div>
\t\t\t\t<div class=\"four wide field\">
\t\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\" />
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<label>State</label>
\t\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t\t<option value=\"\">State</option>
\t\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t\t</select>
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<label>Country</label>
\t\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t\t<input type=\"hidden\" name=\"country\" />
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t<div class=\"item\" data-value=\"af\">
\t\t\t\t\t\t\t<i class=\"af flag\"></i>Afghanistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ax\">
\t\t\t\t\t\t\t<i class=\"ax flag\"></i>Aland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"al\">
\t\t\t\t\t\t\t<i class=\"al flag\"></i>Albania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dz\">
\t\t\t\t\t\t\t<i class=\"dz flag\"></i>Algeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"as\">
\t\t\t\t\t\t\t<i class=\"as flag\"></i>American Samoa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ad\">
\t\t\t\t\t\t\t<i class=\"ad flag\"></i>Andorra
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ao\">
\t\t\t\t\t\t\t<i class=\"ao flag\"></i>Angola
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ai\">
\t\t\t\t\t\t\t<i class=\"ai flag\"></i>Anguilla
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ag\">
\t\t\t\t\t\t\t<i class=\"ag flag\"></i>Antigua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ar\">
\t\t\t\t\t\t\t<i class=\"ar flag\"></i>Argentina
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"am\">
\t\t\t\t\t\t\t<i class=\"am flag\"></i>Armenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"au\">
\t\t\t\t\t\t\t<i class=\"au flag\"></i>Australia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"at\">
\t\t\t\t\t\t\t<i class=\"at flag\"></i>Austria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"az\">
\t\t\t\t\t\t\t<i class=\"az flag\"></i>Azerbaijan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bs\">
\t\t\t\t\t\t\t<i class=\"bs flag\"></i>Bahamas
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bh\">
\t\t\t\t\t\t\t<i class=\"bh flag\"></i>Bahrain
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bd\">
\t\t\t\t\t\t\t<i class=\"bd flag\"></i>Bangladesh
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bb\">
\t\t\t\t\t\t\t<i class=\"bb flag\"></i>Barbados
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"by\">
\t\t\t\t\t\t\t<i class=\"by flag\"></i>Belarus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"be\">
\t\t\t\t\t\t\t<i class=\"be flag\"></i>Belgium
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bz\">
\t\t\t\t\t\t\t<i class=\"bz flag\"></i>Belize
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bm\">
\t\t\t\t\t\t\t<i class=\"bm flag\"></i>Bermuda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bt\">
\t\t\t\t\t\t\t<i class=\"bt flag\"></i>Bhutan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bo\">
\t\t\t\t\t\t\t<i class=\"bo flag\"></i>Bolivia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ba\">
\t\t\t\t\t\t\t<i class=\"ba flag\"></i>Bosnia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bw\">
\t\t\t\t\t\t\t<i class=\"bw flag\"></i>Botswana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bv\">
\t\t\t\t\t\t\t<i class=\"bv flag\"></i>Bouvet Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"br\">
\t\t\t\t\t\t\t<i class=\"br flag\"></i>Brazil
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vg\">
\t\t\t\t\t\t\t<i class=\"vg flag\"></i>British Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bn\">
\t\t\t\t\t\t\t<i class=\"bn flag\"></i>Brunei
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bg\">
\t\t\t\t\t\t\t<i class=\"bg flag\"></i>Bulgaria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bf\">
\t\t\t\t\t\t\t<i class=\"bf flag\"></i>Burkina Faso
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"bi\">
\t\t\t\t\t\t\t<i class=\"bi flag\"></i>Burundi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tc\">
\t\t\t\t\t\t\t<i class=\"tc flag\"></i>Caicos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kh\">
\t\t\t\t\t\t\t<i class=\"kh flag\"></i>Cambodia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cm\">
\t\t\t\t\t\t\t<i class=\"cm flag\"></i>Cameroon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ca\">
\t\t\t\t\t\t\t<i class=\"ca flag\"></i>Canada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cv\">
\t\t\t\t\t\t\t<i class=\"cv flag\"></i>Cape Verde
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ky\">
\t\t\t\t\t\t\t<i class=\"ky flag\"></i>Cayman Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cf\">
\t\t\t\t\t\t\t<i class=\"cf flag\"></i>Central African Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cx\">
\t\t\t\t\t\t\t<i class=\"cx flag\"></i>Christmas Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cc\">
\t\t\t\t\t\t\t<i class=\"cc flag\"></i>Cocos Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"co\">
\t\t\t\t\t\t\t<i class=\"co flag\"></i>Colombia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"km\">
\t\t\t\t\t\t\t<i class=\"km flag\"></i>Comoros
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cg\">
\t\t\t\t\t\t\t<i class=\"cg flag\"></i>Congo Brazzaville
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ck\">
\t\t\t\t\t\t\t<i class=\"ck flag\"></i>Cook Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cr\">
\t\t\t\t\t\t\t<i class=\"cr flag\"></i>Costa Rica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ci\">
\t\t\t\t\t\t\t<i class=\"ci flag\"></i>Cote Divoire
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hr\">
\t\t\t\t\t\t\t<i class=\"hr flag\"></i>Croatia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cy\">
\t\t\t\t\t\t\t<i class=\"cy flag\"></i>Cyprus
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cz\">
\t\t\t\t\t\t\t<i class=\"cz flag\"></i>Czech Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dk\">
\t\t\t\t\t\t\t<i class=\"dk flag\"></i>Denmark
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dj\">
\t\t\t\t\t\t\t<i class=\"dj flag\"></i>Djibouti
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"dm\">
\t\t\t\t\t\t\t<i class=\"dm flag\"></i>Dominica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"do\">
\t\t\t\t\t\t\t<i class=\"do flag\"></i>Dominican Republic
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ec\">
\t\t\t\t\t\t\t<i class=\"ec flag\"></i>Ecuador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sv\">
\t\t\t\t\t\t\t<i class=\"sv flag\"></i>El Salvador
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gb\">
\t\t\t\t\t\t\t<i class=\"gb flag\"></i>England
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gq\">
\t\t\t\t\t\t\t<i class=\"gq flag\"></i>Equatorial Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"er\">
\t\t\t\t\t\t\t<i class=\"er flag\"></i>Eritrea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ee\">
\t\t\t\t\t\t\t<i class=\"ee flag\"></i>Estonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"et\">
\t\t\t\t\t\t\t<i class=\"et flag\"></i>Ethiopia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eu\">
\t\t\t\t\t\t\t<i class=\"eu flag\"></i>European Union
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fk\">
\t\t\t\t\t\t\t<i class=\"fk flag\"></i>Falkland Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fo\">
\t\t\t\t\t\t\t<i class=\"fo flag\"></i>Faroe Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fi\">
\t\t\t\t\t\t\t<i class=\"fi flag\"></i>Finland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fr\">
\t\t\t\t\t\t\t<i class=\"fr flag\"></i>France
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gf\">
\t\t\t\t\t\t\t<i class=\"gf flag\"></i>French Guiana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pf\">
\t\t\t\t\t\t\t<i class=\"pf flag\"></i>French Polynesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tf\">
\t\t\t\t\t\t\t<i class=\"tf flag\"></i>French Territories
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gm\">
\t\t\t\t\t\t\t<i class=\"gm flag\"></i>Gambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ge\">
\t\t\t\t\t\t\t<i class=\"ge flag\"></i>Georgia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"de\">
\t\t\t\t\t\t\t<i class=\"de flag\"></i>Germany
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gi\">
\t\t\t\t\t\t\t<i class=\"gi flag\"></i>Gibraltar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gr\">
\t\t\t\t\t\t\t<i class=\"gr flag\"></i>Greece
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gl\">
\t\t\t\t\t\t\t<i class=\"gl flag\"></i>Greenland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gd\">
\t\t\t\t\t\t\t<i class=\"gd flag\"></i>Grenada
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gp\">
\t\t\t\t\t\t\t<i class=\"gp flag\"></i>Guadeloupe
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gt\">
\t\t\t\t\t\t\t<i class=\"gt flag\"></i>Guatemala
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gw\">
\t\t\t\t\t\t\t<i class=\"gw flag\"></i>Guinea-Bissau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gn\">
\t\t\t\t\t\t\t<i class=\"gn flag\"></i>Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gy\">
\t\t\t\t\t\t\t<i class=\"gy flag\"></i>Guyana
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hm\">
\t\t\t\t\t\t\t<i class=\"hm flag\"></i>Heard Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hn\">
\t\t\t\t\t\t\t<i class=\"hn flag\"></i>Honduras
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hk\">
\t\t\t\t\t\t\t<i class=\"hk flag\"></i>Hong Kong
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"hu\">
\t\t\t\t\t\t\t<i class=\"hu flag\"></i>Hungary
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"is\">
\t\t\t\t\t\t\t<i class=\"is flag\"></i>Iceland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"io\">
\t\t\t\t\t\t\t<i class=\"io flag\"></i>Indian Ocean Territory
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"id\">
\t\t\t\t\t\t\t<i class=\"id flag\"></i>Indonesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ie\">
\t\t\t\t\t\t\t<i class=\"ie flag\"></i>Ireland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"il\">
\t\t\t\t\t\t\t<i class=\"il flag\"></i>Israel
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jm\">
\t\t\t\t\t\t\t<i class=\"jm flag\"></i>Jamaica
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"jo\">
\t\t\t\t\t\t\t<i class=\"jo flag\"></i>Jordan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kz\">
\t\t\t\t\t\t\t<i class=\"kz flag\"></i>Kazakhstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ki\">
\t\t\t\t\t\t\t<i class=\"ki flag\"></i>Kiribati
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kw\">
\t\t\t\t\t\t\t<i class=\"kw flag\"></i>Kuwait
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kg\">
\t\t\t\t\t\t\t<i class=\"kg flag\"></i>Kyrgyzstan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lv\">
\t\t\t\t\t\t\t<i class=\"lv flag\"></i>Latvia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lb\">
\t\t\t\t\t\t\t<i class=\"lb flag\"></i>Lebanon
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ls\">
\t\t\t\t\t\t\t<i class=\"ls flag\"></i>Lesotho
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lr\">
\t\t\t\t\t\t\t<i class=\"lr flag\"></i>Liberia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"li\">
\t\t\t\t\t\t\t<i class=\"li flag\"></i>Liechtenstein
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lt\">
\t\t\t\t\t\t\t<i class=\"lt flag\"></i>Lithuania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lu\">
\t\t\t\t\t\t\t<i class=\"lu flag\"></i>Luxembourg
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mk\">
\t\t\t\t\t\t\t<i class=\"mk flag\"></i>Macedonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mg\">
\t\t\t\t\t\t\t<i class=\"mg flag\"></i>Madagascar
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mw\">
\t\t\t\t\t\t\t<i class=\"mw flag\"></i>Malawi
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"my\">
\t\t\t\t\t\t\t<i class=\"my flag\"></i>Malaysia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mv\">
\t\t\t\t\t\t\t<i class=\"mv flag\"></i>Maldives
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mh\">
\t\t\t\t\t\t\t<i class=\"mh flag\"></i>Marshall Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mq\">
\t\t\t\t\t\t\t<i class=\"mq flag\"></i>Martinique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mr\">
\t\t\t\t\t\t\t<i class=\"mr flag\"></i>Mauritania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mu\">
\t\t\t\t\t\t\t<i class=\"mu flag\"></i>Mauritius
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"yt\">
\t\t\t\t\t\t\t<i class=\"yt flag\"></i>Mayotte
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mx\">
\t\t\t\t\t\t\t<i class=\"mx flag\"></i>Mexico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"fm\">
\t\t\t\t\t\t\t<i class=\"fm flag\"></i>Micronesia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"md\">
\t\t\t\t\t\t\t<i class=\"md flag\"></i>Moldova
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mc\">
\t\t\t\t\t\t\t<i class=\"mc flag\"></i>Monaco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mn\">
\t\t\t\t\t\t\t<i class=\"mn flag\"></i>Mongolia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"me\">
\t\t\t\t\t\t\t<i class=\"me flag\"></i>Montenegro
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ms\">
\t\t\t\t\t\t\t<i class=\"ms flag\"></i>Montserrat
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ma\">
\t\t\t\t\t\t\t<i class=\"ma flag\"></i>Morocco
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mz\">
\t\t\t\t\t\t\t<i class=\"mz flag\"></i>Mozambique
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"na\">
\t\t\t\t\t\t\t<i class=\"na flag\"></i>Namibia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"an\">
\t\t\t\t\t\t\t<i class=\"an flag\"></i>Netherlands Antilles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nl\">
\t\t\t\t\t\t\t<i class=\"nl flag\"></i>Netherlands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nc\">
\t\t\t\t\t\t\t<i class=\"nc flag\"></i>New Caledonia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pg\">
\t\t\t\t\t\t\t<i class=\"pg flag\"></i>New Guinea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nz\">
\t\t\t\t\t\t\t<i class=\"nz flag\"></i>New Zealand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ni\">
\t\t\t\t\t\t\t<i class=\"ni flag\"></i>Nicaragua
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ng\">
\t\t\t\t\t\t\t<i class=\"ng flag\"></i>Nigeria
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"nf\">
\t\t\t\t\t\t\t<i class=\"nf flag\"></i>Norfolk Island
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kp\">
\t\t\t\t\t\t\t<i class=\"kp flag\"></i>North Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"mp\">
\t\t\t\t\t\t\t<i class=\"mp flag\"></i>Northern Mariana Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"no\">
\t\t\t\t\t\t\t<i class=\"no flag\"></i>Norway
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pk\">
\t\t\t\t\t\t\t<i class=\"pk flag\"></i>Pakistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ps\">
\t\t\t\t\t\t\t<i class=\"ps flag\"></i>Palestine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pa\">
\t\t\t\t\t\t\t<i class=\"pa flag\"></i>Panama
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"py\">
\t\t\t\t\t\t\t<i class=\"py flag\"></i>Paraguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ph\">
\t\t\t\t\t\t\t<i class=\"ph flag\"></i>Philippines
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pn\">
\t\t\t\t\t\t\t<i class=\"pn flag\"></i>Pitcairn Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pl\">
\t\t\t\t\t\t\t<i class=\"pl flag\"></i>Poland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pt\">
\t\t\t\t\t\t\t<i class=\"pt flag\"></i>Portugal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pr\">
\t\t\t\t\t\t\t<i class=\"pr flag\"></i>Puerto Rico
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"re\">
\t\t\t\t\t\t\t<i class=\"re flag\"></i>Reunion
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ro\">
\t\t\t\t\t\t\t<i class=\"ro flag\"></i>Romania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ru\">
\t\t\t\t\t\t\t<i class=\"ru flag\"></i>Russia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rw\">
\t\t\t\t\t\t\t<i class=\"rw flag\"></i>Rwanda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sh\">
\t\t\t\t\t\t\t<i class=\"sh flag\"></i>Saint Helena
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kn\">
\t\t\t\t\t\t\t<i class=\"kn flag\"></i>Saint Kitts and Nevis
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lc\">
\t\t\t\t\t\t\t<i class=\"lc flag\"></i>Saint Lucia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"pm\">
\t\t\t\t\t\t\t<i class=\"pm flag\"></i>Saint Pierre
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vc\">
\t\t\t\t\t\t\t<i class=\"vc flag\"></i>Saint Vincent
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sm\">
\t\t\t\t\t\t\t<i class=\"sm flag\"></i>San Marino
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"gs\">
\t\t\t\t\t\t\t<i class=\"gs flag\"></i>Sandwich Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"st\">
\t\t\t\t\t\t\t<i class=\"st flag\"></i>Sao Tome
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sa\">
\t\t\t\t\t\t\t<i class=\"sa flag\"></i>Saudi Arabia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sn\">
\t\t\t\t\t\t\t<i class=\"sn flag\"></i>Senegal
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"cs\">
\t\t\t\t\t\t\t<i class=\"cs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"rs\">
\t\t\t\t\t\t\t<i class=\"rs flag\"></i>Serbia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sc\">
\t\t\t\t\t\t\t<i class=\"sc flag\"></i>Seychelles
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sl\">
\t\t\t\t\t\t\t<i class=\"sl flag\"></i>Sierra Leone
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sg\">
\t\t\t\t\t\t\t<i class=\"sg flag\"></i>Singapore
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sk\">
\t\t\t\t\t\t\t<i class=\"sk flag\"></i>Slovakia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"si\">
\t\t\t\t\t\t\t<i class=\"si flag\"></i>Slovenia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sb\">
\t\t\t\t\t\t\t<i class=\"sb flag\"></i>Solomon Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"so\">
\t\t\t\t\t\t\t<i class=\"so flag\"></i>Somalia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"za\">
\t\t\t\t\t\t\t<i class=\"za flag\"></i>South Africa
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"kr\">
\t\t\t\t\t\t\t<i class=\"kr flag\"></i>South Korea
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"lk\">
\t\t\t\t\t\t\t<i class=\"lk flag\"></i>Sri Lanka
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sr\">
\t\t\t\t\t\t\t<i class=\"sr flag\"></i>Suriname
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sj\">
\t\t\t\t\t\t\t<i class=\"sj flag\"></i>Svalbard
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sz\">
\t\t\t\t\t\t\t<i class=\"sz flag\"></i>Swaziland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"se\">
\t\t\t\t\t\t\t<i class=\"se flag\"></i>Sweden
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ch\">
\t\t\t\t\t\t\t<i class=\"ch flag\"></i>Switzerland
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tw\">
\t\t\t\t\t\t\t<i class=\"tw flag\"></i>Taiwan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tj\">
\t\t\t\t\t\t\t<i class=\"tj flag\"></i>Tajikistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tz\">
\t\t\t\t\t\t\t<i class=\"tz flag\"></i>Tanzania
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"th\">
\t\t\t\t\t\t\t<i class=\"th flag\"></i>Thailand
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tl\">
\t\t\t\t\t\t\t<i class=\"tl flag\"></i>Timorleste
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tk\">
\t\t\t\t\t\t\t<i class=\"tk flag\"></i>Tokelau
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tt\">
\t\t\t\t\t\t\t<i class=\"tt flag\"></i>Trinidad
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tn\">
\t\t\t\t\t\t\t<i class=\"tn flag\"></i>Tunisia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tr\">
\t\t\t\t\t\t\t<i class=\"tr flag\"></i>Turkey
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tm\">
\t\t\t\t\t\t\t<i class=\"tm flag\"></i>Turkmenistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"tv\">
\t\t\t\t\t\t\t<i class=\"tv flag\"></i>Tuvalu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ug\">
\t\t\t\t\t\t\t<i class=\"ug flag\"></i>Uganda
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ua\">
\t\t\t\t\t\t\t<i class=\"ua flag\"></i>Ukraine
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ae\">
\t\t\t\t\t\t\t<i class=\"ae flag\"></i>United Arab Emirates
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"us\">
\t\t\t\t\t\t\t<i class=\"us flag\"></i>United States
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uy\">
\t\t\t\t\t\t\t<i class=\"uy flag\"></i>Uruguay
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"um\">
\t\t\t\t\t\t\t<i class=\"um flag\"></i>Us Minor Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vi\">
\t\t\t\t\t\t\t<i class=\"vi flag\"></i>Us Virgin Islands
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"uz\">
\t\t\t\t\t\t\t<i class=\"uz flag\"></i>Uzbekistan
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vu\">
\t\t\t\t\t\t\t<i class=\"vu flag\"></i>Vanuatu
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"va\">
\t\t\t\t\t\t\t<i class=\"va flag\"></i>Vatican City
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ve\">
\t\t\t\t\t\t\t<i class=\"ve flag\"></i>Venezuela
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"vn\">
\t\t\t\t\t\t\t<i class=\"vn flag\"></i>Vietnam
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"wf\">
\t\t\t\t\t\t\t<i class=\"wf flag\"></i>Wallis and Futuna
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"eh\">
\t\t\t\t\t\t\t<i class=\"eh flag\"></i>Western Sahara
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zm\">
\t\t\t\t\t\t\t<i class=\"zm flag\"></i>Zambia
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"item\" data-value=\"zw\">
\t\t\t\t\t\t\t<i class=\"zw flag\"></i>Zimbabwe
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t\t<div class=\"field\">
\t\t\t<label>Card Type</label>
\t\t\t<div class=\"ui selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"card[type]\" />
\t\t\t\t<div class=\"default text\">Type</div>
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\t\tVisa
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\t\tAmerican Express
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\t\tDiscover
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"fields\">
\t\t\t<div class=\"seven wide field\">
\t\t\t\t<label>Card Number</label>
\t\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\" />
\t\t\t</div>
\t\t\t<div class=\"three wide field\">
\t\t\t\t<label>CVC</label>
\t\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\" />
\t\t\t</div>
\t\t\t<div class=\"six wide field\">
\t\t\t\t<label>Expiration</label>
\t\t\t\t<div class=\"two fields\">
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"field\">
\t\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\" />
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<h4 class=\"ui dividing header\">Receipt</h4>
\t\t<div class=\"field\">
\t\t\t<label>Send Receipt To:</label>
\t\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"receipt\" />
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\" />
\t\t\t\t\t\tJenny Hess
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\" />
\t\t\t\t\t\tElliot Fu
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\" />
\t\t\t\t\t\tStevie Feliciano
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\" />
\t\t\t\t\t\tChristian
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\" />
\t\t\t\t\t\tMatt
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\" />
\t\t\t\t\t\tJusten Kitsune
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui segment\">
\t\t\t<div class=\"field\">
\t\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\" />
\t\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
\t</form>
</div>
{% endblock %}
", "modules/main.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\modules\\main.html");
    }
}
