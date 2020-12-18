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

/* main.html */
class __TwigTemplate_dc249a310d4ad4d6e328c466fcbe6fce1cbfb7b43ec0a13ed3fd7c20586caf89 extends \Twig\Template
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
        $this->parent = $this->loadTemplate("base.html", "main.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "<form class=\"ui form\">
\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t<div class=\"field\">
\t\t<label>Name</label>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\">
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\">
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"field\">
\t\t<label>Billing Address</label>
\t\t<div class=\"fields\">
\t\t\t<div class=\"twelve wide field\">
\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\">
\t\t\t</div>
\t\t\t<div class=\"four wide field\">
\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\">
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"two fields\">
\t\t<div class=\"field\">
\t\t\t<label>State</label>
\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t<option value=\"\">State</option>
\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Country</label>
\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"country\">
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"af\"><i class=\"af flag\"></i>Afghanistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ax\"><i class=\"ax flag\"></i>Aland Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"al\"><i class=\"al flag\"></i>Albania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dz\"><i class=\"dz flag\"></i>Algeria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"as\"><i class=\"as flag\"></i>American Samoa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ad\"><i class=\"ad flag\"></i>Andorra</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ao\"><i class=\"ao flag\"></i>Angola</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ai\"><i class=\"ai flag\"></i>Anguilla</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ag\"><i class=\"ag flag\"></i>Antigua</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ar\"><i class=\"ar flag\"></i>Argentina</div>
\t\t\t\t\t<div class=\"item\" data-value=\"am\"><i class=\"am flag\"></i>Armenia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t<div class=\"item\" data-value=\"au\"><i class=\"au flag\"></i>Australia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"at\"><i class=\"at flag\"></i>Austria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"az\"><i class=\"az flag\"></i>Azerbaijan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bs\"><i class=\"bs flag\"></i>Bahamas</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bh\"><i class=\"bh flag\"></i>Bahrain</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bd\"><i class=\"bd flag\"></i>Bangladesh</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bb\"><i class=\"bb flag\"></i>Barbados</div>
\t\t\t\t\t<div class=\"item\" data-value=\"by\"><i class=\"by flag\"></i>Belarus</div>
\t\t\t\t\t<div class=\"item\" data-value=\"be\"><i class=\"be flag\"></i>Belgium</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bz\"><i class=\"bz flag\"></i>Belize</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bm\"><i class=\"bm flag\"></i>Bermuda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bt\"><i class=\"bt flag\"></i>Bhutan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bo\"><i class=\"bo flag\"></i>Bolivia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ba\"><i class=\"ba flag\"></i>Bosnia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bw\"><i class=\"bw flag\"></i>Botswana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bv\"><i class=\"bv flag\"></i>Bouvet Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"br\"><i class=\"br flag\"></i>Brazil</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vg\"><i class=\"vg flag\"></i>British Virgin Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bn\"><i class=\"bn flag\"></i>Brunei</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bg\"><i class=\"bg flag\"></i>Bulgaria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bf\"><i class=\"bf flag\"></i>Burkina Faso</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bi\"><i class=\"bi flag\"></i>Burundi</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tc\"><i class=\"tc flag\"></i>Caicos Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kh\"><i class=\"kh flag\"></i>Cambodia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cm\"><i class=\"cm flag\"></i>Cameroon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ca\"><i class=\"ca flag\"></i>Canada</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cv\"><i class=\"cv flag\"></i>Cape Verde</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ky\"><i class=\"ky flag\"></i>Cayman Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cf\"><i class=\"cf flag\"></i>Central African Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cx\"><i class=\"cx flag\"></i>Christmas Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cc\"><i class=\"cc flag\"></i>Cocos Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"co\"><i class=\"co flag\"></i>Colombia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"km\"><i class=\"km flag\"></i>Comoros</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cg\"><i class=\"cg flag\"></i>Congo Brazzaville</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ck\"><i class=\"ck flag\"></i>Cook Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cr\"><i class=\"cr flag\"></i>Costa Rica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ci\"><i class=\"ci flag\"></i>Cote Divoire</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hr\"><i class=\"hr flag\"></i>Croatia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cy\"><i class=\"cy flag\"></i>Cyprus</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cz\"><i class=\"cz flag\"></i>Czech Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dk\"><i class=\"dk flag\"></i>Denmark</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dj\"><i class=\"dj flag\"></i>Djibouti</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dm\"><i class=\"dm flag\"></i>Dominica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"do\"><i class=\"do flag\"></i>Dominican Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ec\"><i class=\"ec flag\"></i>Ecuador</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sv\"><i class=\"sv flag\"></i>El Salvador</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gb\"><i class=\"gb flag\"></i>England</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gq\"><i class=\"gq flag\"></i>Equatorial Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"er\"><i class=\"er flag\"></i>Eritrea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ee\"><i class=\"ee flag\"></i>Estonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"et\"><i class=\"et flag\"></i>Ethiopia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eu\"><i class=\"eu flag\"></i>European Union</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fk\"><i class=\"fk flag\"></i>Falkland Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fo\"><i class=\"fo flag\"></i>Faroe Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fi\"><i class=\"fi flag\"></i>Finland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fr\"><i class=\"fr flag\"></i>France</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gf\"><i class=\"gf flag\"></i>French Guiana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pf\"><i class=\"pf flag\"></i>French Polynesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tf\"><i class=\"tf flag\"></i>French Territories</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gm\"><i class=\"gm flag\"></i>Gambia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ge\"><i class=\"ge flag\"></i>Georgia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"de\"><i class=\"de flag\"></i>Germany</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gi\"><i class=\"gi flag\"></i>Gibraltar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gr\"><i class=\"gr flag\"></i>Greece</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gl\"><i class=\"gl flag\"></i>Greenland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gd\"><i class=\"gd flag\"></i>Grenada</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gp\"><i class=\"gp flag\"></i>Guadeloupe</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gt\"><i class=\"gt flag\"></i>Guatemala</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gw\"><i class=\"gw flag\"></i>Guinea-Bissau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gn\"><i class=\"gn flag\"></i>Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gy\"><i class=\"gy flag\"></i>Guyana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hm\"><i class=\"hm flag\"></i>Heard Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hn\"><i class=\"hn flag\"></i>Honduras</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hk\"><i class=\"hk flag\"></i>Hong Kong</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hu\"><i class=\"hu flag\"></i>Hungary</div>
\t\t\t\t\t<div class=\"item\" data-value=\"is\"><i class=\"is flag\"></i>Iceland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t<div class=\"item\" data-value=\"io\"><i class=\"io flag\"></i>Indian Ocean Territory</div>
\t\t\t\t\t<div class=\"item\" data-value=\"id\"><i class=\"id flag\"></i>Indonesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ie\"><i class=\"ie flag\"></i>Ireland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"il\"><i class=\"il flag\"></i>Israel</div>
\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jm\"><i class=\"jm flag\"></i>Jamaica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jo\"><i class=\"jo flag\"></i>Jordan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kz\"><i class=\"kz flag\"></i>Kazakhstan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ki\"><i class=\"ki flag\"></i>Kiribati</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kw\"><i class=\"kw flag\"></i>Kuwait</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kg\"><i class=\"kg flag\"></i>Kyrgyzstan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lv\"><i class=\"lv flag\"></i>Latvia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lb\"><i class=\"lb flag\"></i>Lebanon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ls\"><i class=\"ls flag\"></i>Lesotho</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lr\"><i class=\"lr flag\"></i>Liberia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t<div class=\"item\" data-value=\"li\"><i class=\"li flag\"></i>Liechtenstein</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lt\"><i class=\"lt flag\"></i>Lithuania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lu\"><i class=\"lu flag\"></i>Luxembourg</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mk\"><i class=\"mk flag\"></i>Macedonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mg\"><i class=\"mg flag\"></i>Madagascar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mw\"><i class=\"mw flag\"></i>Malawi</div>
\t\t\t\t\t<div class=\"item\" data-value=\"my\"><i class=\"my flag\"></i>Malaysia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mv\"><i class=\"mv flag\"></i>Maldives</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mh\"><i class=\"mh flag\"></i>Marshall Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mq\"><i class=\"mq flag\"></i>Martinique</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mr\"><i class=\"mr flag\"></i>Mauritania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mu\"><i class=\"mu flag\"></i>Mauritius</div>
\t\t\t\t\t<div class=\"item\" data-value=\"yt\"><i class=\"yt flag\"></i>Mayotte</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mx\"><i class=\"mx flag\"></i>Mexico</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fm\"><i class=\"fm flag\"></i>Micronesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"md\"><i class=\"md flag\"></i>Moldova</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mc\"><i class=\"mc flag\"></i>Monaco</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mn\"><i class=\"mn flag\"></i>Mongolia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"me\"><i class=\"me flag\"></i>Montenegro</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ms\"><i class=\"ms flag\"></i>Montserrat</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ma\"><i class=\"ma flag\"></i>Morocco</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mz\"><i class=\"mz flag\"></i>Mozambique</div>
\t\t\t\t\t<div class=\"item\" data-value=\"na\"><i class=\"na flag\"></i>Namibia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"an\"><i class=\"an flag\"></i>Netherlands Antilles</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nl\"><i class=\"nl flag\"></i>Netherlands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nc\"><i class=\"nc flag\"></i>New Caledonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pg\"><i class=\"pg flag\"></i>New Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nz\"><i class=\"nz flag\"></i>New Zealand</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ni\"><i class=\"ni flag\"></i>Nicaragua</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ng\"><i class=\"ng flag\"></i>Nigeria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nf\"><i class=\"nf flag\"></i>Norfolk Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kp\"><i class=\"kp flag\"></i>North Korea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mp\"><i class=\"mp flag\"></i>Northern Mariana Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"no\"><i class=\"no flag\"></i>Norway</div>
\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pk\"><i class=\"pk flag\"></i>Pakistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ps\"><i class=\"ps flag\"></i>Palestine</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pa\"><i class=\"pa flag\"></i>Panama</div>
\t\t\t\t\t<div class=\"item\" data-value=\"py\"><i class=\"py flag\"></i>Paraguay</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ph\"><i class=\"ph flag\"></i>Philippines</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pn\"><i class=\"pn flag\"></i>Pitcairn Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pl\"><i class=\"pl flag\"></i>Poland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pt\"><i class=\"pt flag\"></i>Portugal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pr\"><i class=\"pr flag\"></i>Puerto Rico</div>
\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"re\"><i class=\"re flag\"></i>Reunion</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ro\"><i class=\"ro flag\"></i>Romania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ru\"><i class=\"ru flag\"></i>Russia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"rw\"><i class=\"rw flag\"></i>Rwanda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sh\"><i class=\"sh flag\"></i>Saint Helena</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kn\"><i class=\"kn flag\"></i>Saint Kitts and Nevis</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lc\"><i class=\"lc flag\"></i>Saint Lucia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pm\"><i class=\"pm flag\"></i>Saint Pierre</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vc\"><i class=\"vc flag\"></i>Saint Vincent</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sm\"><i class=\"sm flag\"></i>San Marino</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gs\"><i class=\"gs flag\"></i>Sandwich Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"st\"><i class=\"st flag\"></i>Sao Tome</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sa\"><i class=\"sa flag\"></i>Saudi Arabia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sn\"><i class=\"sn flag\"></i>Senegal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cs\"><i class=\"cs flag\"></i>Serbia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"rs\"><i class=\"rs flag\"></i>Serbia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sc\"><i class=\"sc flag\"></i>Seychelles</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sl\"><i class=\"sl flag\"></i>Sierra Leone</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sg\"><i class=\"sg flag\"></i>Singapore</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sk\"><i class=\"sk flag\"></i>Slovakia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"si\"><i class=\"si flag\"></i>Slovenia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sb\"><i class=\"sb flag\"></i>Solomon Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"so\"><i class=\"so flag\"></i>Somalia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"za\"><i class=\"za flag\"></i>South Africa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kr\"><i class=\"kr flag\"></i>South Korea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lk\"><i class=\"lk flag\"></i>Sri Lanka</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sr\"><i class=\"sr flag\"></i>Suriname</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sj\"><i class=\"sj flag\"></i>Svalbard</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sz\"><i class=\"sz flag\"></i>Swaziland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"se\"><i class=\"se flag\"></i>Sweden</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ch\"><i class=\"ch flag\"></i>Switzerland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tw\"><i class=\"tw flag\"></i>Taiwan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tj\"><i class=\"tj flag\"></i>Tajikistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tz\"><i class=\"tz flag\"></i>Tanzania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"th\"><i class=\"th flag\"></i>Thailand</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tl\"><i class=\"tl flag\"></i>Timorleste</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tk\"><i class=\"tk flag\"></i>Tokelau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tt\"><i class=\"tt flag\"></i>Trinidad</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tn\"><i class=\"tn flag\"></i>Tunisia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tr\"><i class=\"tr flag\"></i>Turkey</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tm\"><i class=\"tm flag\"></i>Turkmenistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tv\"><i class=\"tv flag\"></i>Tuvalu</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ug\"><i class=\"ug flag\"></i>Uganda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ua\"><i class=\"ua flag\"></i>Ukraine</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ae\"><i class=\"ae flag\"></i>United Arab Emirates</div>
\t\t\t\t\t<div class=\"item\" data-value=\"us\"><i class=\"us flag\"></i>United States</div>
\t\t\t\t\t<div class=\"item\" data-value=\"uy\"><i class=\"uy flag\"></i>Uruguay</div>
\t\t\t\t\t<div class=\"item\" data-value=\"um\"><i class=\"um flag\"></i>Us Minor Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vi\"><i class=\"vi flag\"></i>Us Virgin Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"uz\"><i class=\"uz flag\"></i>Uzbekistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vu\"><i class=\"vu flag\"></i>Vanuatu</div>
\t\t\t\t\t<div class=\"item\" data-value=\"va\"><i class=\"va flag\"></i>Vatican City</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ve\"><i class=\"ve flag\"></i>Venezuela</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vn\"><i class=\"vn flag\"></i>Vietnam</div>
\t\t\t\t\t<div class=\"item\" data-value=\"wf\"><i class=\"wf flag\"></i>Wallis and Futuna</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eh\"><i class=\"eh flag\"></i>Western Sahara</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t<div class=\"item\" data-value=\"zm\"><i class=\"zm flag\"></i>Zambia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"zw\"><i class=\"zw flag\"></i>Zimbabwe</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t<div class=\"field\">
\t\t<label>Card Type</label>
\t\t<div class=\"ui selection dropdown\">
\t\t\t<input type=\"hidden\" name=\"card[type]\">
\t\t\t<div class=\"default text\">Type</div>
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t<div class=\"menu\">
\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\tVisa
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\tAmerican Express
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\tDiscover
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"fields\">
\t\t<div class=\"seven wide field\">
\t\t\t<label>Card Number</label>
\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\">
\t\t</div>
\t\t<div class=\"three wide field\">
\t\t\t<label>CVC</label>
\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\">
\t\t</div>
\t\t<div class=\"six wide field\">
\t\t\t<label>Expiration</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\">
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<h4 class=\"ui dividing header\">Receipt</h4>
\t<div class=\"field\">
\t\t<label>Send Receipt To:</label>
\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t<input type=\"hidden\" name=\"receipt\">
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t<div class=\"menu\">
\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\">
\t\t\t\t\tJenny Hess
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\">
\t\t\t\t\tElliot Fu
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\">
\t\t\t\t\tStevie Feliciano
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\">
\t\t\t\t\tChristian
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\">
\t\t\t\t\tMatt
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\">
\t\t\t\t\tJusten Kitsune
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"ui segment\">
\t\t<div class=\"field\">
\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\">
\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
</form>
";
    }

    public function getTemplateName()
    {
        return "main.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 4,  39 => 3,  29 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html' %}

{% block content %}
<form class=\"ui form\">
\t<h4 class=\"ui dividing header\">Shipping Information</h4>
\t<div class=\"field\">
\t\t<label>Name</label>
\t\t<div class=\"two fields\">
\t\t\t<div class=\"field\">
\t\t\t\t<input type=\"text\" name=\"shipping[first-name]\" placeholder=\"First Name\">
\t\t\t</div>
\t\t\t<div class=\"field\">
\t\t\t\t<input type=\"text\" name=\"shipping[last-name]\" placeholder=\"Last Name\">
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"field\">
\t\t<label>Billing Address</label>
\t\t<div class=\"fields\">
\t\t\t<div class=\"twelve wide field\">
\t\t\t\t<input type=\"text\" name=\"shipping[address]\" placeholder=\"Street Address\">
\t\t\t</div>
\t\t\t<div class=\"four wide field\">
\t\t\t\t<input type=\"text\" name=\"shipping[address-2]\" placeholder=\"Apt #\">
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"two fields\">
\t\t<div class=\"field\">
\t\t\t<label>State</label>
\t\t\t<select class=\"ui fluid dropdown\">
\t\t\t\t<option value=\"\">State</option>
\t\t\t\t<option value=\"AL\">Alabama</option>
\t\t\t\t<option value=\"AK\">Alaska</option>
\t\t\t\t<option value=\"AZ\">Arizona</option>
\t\t\t\t<option value=\"AR\">Arkansas</option>
\t\t\t\t<option value=\"CA\">California</option>
\t\t\t\t<option value=\"CO\">Colorado</option>
\t\t\t\t<option value=\"CT\">Connecticut</option>
\t\t\t\t<option value=\"DE\">Delaware</option>
\t\t\t\t<option value=\"DC\">District Of Columbia</option>
\t\t\t\t<option value=\"FL\">Florida</option>
\t\t\t\t<option value=\"GA\">Georgia</option>
\t\t\t\t<option value=\"HI\">Hawaii</option>
\t\t\t\t<option value=\"ID\">Idaho</option>
\t\t\t\t<option value=\"IL\">Illinois</option>
\t\t\t\t<option value=\"IN\">Indiana</option>
\t\t\t\t<option value=\"IA\">Iowa</option>
\t\t\t\t<option value=\"KS\">Kansas</option>
\t\t\t\t<option value=\"KY\">Kentucky</option>
\t\t\t\t<option value=\"LA\">Louisiana</option>
\t\t\t\t<option value=\"ME\">Maine</option>
\t\t\t\t<option value=\"MD\">Maryland</option>
\t\t\t\t<option value=\"MA\">Massachusetts</option>
\t\t\t\t<option value=\"MI\">Michigan</option>
\t\t\t\t<option value=\"MN\">Minnesota</option>
\t\t\t\t<option value=\"MS\">Mississippi</option>
\t\t\t\t<option value=\"MO\">Missouri</option>
\t\t\t\t<option value=\"MT\">Montana</option>
\t\t\t\t<option value=\"NE\">Nebraska</option>
\t\t\t\t<option value=\"NV\">Nevada</option>
\t\t\t\t<option value=\"NH\">New Hampshire</option>
\t\t\t\t<option value=\"NJ\">New Jersey</option>
\t\t\t\t<option value=\"NM\">New Mexico</option>
\t\t\t\t<option value=\"NY\">New York</option>
\t\t\t\t<option value=\"NC\">North Carolina</option>
\t\t\t\t<option value=\"ND\">North Dakota</option>
\t\t\t\t<option value=\"OH\">Ohio</option>
\t\t\t\t<option value=\"OK\">Oklahoma</option>
\t\t\t\t<option value=\"OR\">Oregon</option>
\t\t\t\t<option value=\"PA\">Pennsylvania</option>
\t\t\t\t<option value=\"RI\">Rhode Island</option>
\t\t\t\t<option value=\"SC\">South Carolina</option>
\t\t\t\t<option value=\"SD\">South Dakota</option>
\t\t\t\t<option value=\"TN\">Tennessee</option>
\t\t\t\t<option value=\"TX\">Texas</option>
\t\t\t\t<option value=\"UT\">Utah</option>
\t\t\t\t<option value=\"VT\">Vermont</option>
\t\t\t\t<option value=\"VA\">Virginia</option>
\t\t\t\t<option value=\"WA\">Washington</option>
\t\t\t\t<option value=\"WV\">West Virginia</option>
\t\t\t\t<option value=\"WI\">Wisconsin</option>
\t\t\t\t<option value=\"WY\">Wyoming</option>
\t\t\t</select>
\t\t</div>
\t\t<div class=\"field\">
\t\t\t<label>Country</label>
\t\t\t<div class=\"ui fluid search selection dropdown\">
\t\t\t\t<input type=\"hidden\" name=\"country\">
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t<div class=\"default text\">Select Country</div>
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t<div class=\"item\" data-value=\"af\"><i class=\"af flag\"></i>Afghanistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ax\"><i class=\"ax flag\"></i>Aland Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"al\"><i class=\"al flag\"></i>Albania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dz\"><i class=\"dz flag\"></i>Algeria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"as\"><i class=\"as flag\"></i>American Samoa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ad\"><i class=\"ad flag\"></i>Andorra</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ao\"><i class=\"ao flag\"></i>Angola</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ai\"><i class=\"ai flag\"></i>Anguilla</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ag\"><i class=\"ag flag\"></i>Antigua</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ar\"><i class=\"ar flag\"></i>Argentina</div>
\t\t\t\t\t<div class=\"item\" data-value=\"am\"><i class=\"am flag\"></i>Armenia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"aw\"><i class=\"aw flag\"></i>Aruba</div>
\t\t\t\t\t<div class=\"item\" data-value=\"au\"><i class=\"au flag\"></i>Australia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"at\"><i class=\"at flag\"></i>Austria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"az\"><i class=\"az flag\"></i>Azerbaijan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bs\"><i class=\"bs flag\"></i>Bahamas</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bh\"><i class=\"bh flag\"></i>Bahrain</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bd\"><i class=\"bd flag\"></i>Bangladesh</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bb\"><i class=\"bb flag\"></i>Barbados</div>
\t\t\t\t\t<div class=\"item\" data-value=\"by\"><i class=\"by flag\"></i>Belarus</div>
\t\t\t\t\t<div class=\"item\" data-value=\"be\"><i class=\"be flag\"></i>Belgium</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bz\"><i class=\"bz flag\"></i>Belize</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bj\"><i class=\"bj flag\"></i>Benin</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bm\"><i class=\"bm flag\"></i>Bermuda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bt\"><i class=\"bt flag\"></i>Bhutan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bo\"><i class=\"bo flag\"></i>Bolivia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ba\"><i class=\"ba flag\"></i>Bosnia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bw\"><i class=\"bw flag\"></i>Botswana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bv\"><i class=\"bv flag\"></i>Bouvet Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"br\"><i class=\"br flag\"></i>Brazil</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vg\"><i class=\"vg flag\"></i>British Virgin Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bn\"><i class=\"bn flag\"></i>Brunei</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bg\"><i class=\"bg flag\"></i>Bulgaria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bf\"><i class=\"bf flag\"></i>Burkina Faso</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mm\"><i class=\"mm flag\"></i>Burma</div>
\t\t\t\t\t<div class=\"item\" data-value=\"bi\"><i class=\"bi flag\"></i>Burundi</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tc\"><i class=\"tc flag\"></i>Caicos Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kh\"><i class=\"kh flag\"></i>Cambodia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cm\"><i class=\"cm flag\"></i>Cameroon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ca\"><i class=\"ca flag\"></i>Canada</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cv\"><i class=\"cv flag\"></i>Cape Verde</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ky\"><i class=\"ky flag\"></i>Cayman Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cf\"><i class=\"cf flag\"></i>Central African Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"td\"><i class=\"td flag\"></i>Chad</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cl\"><i class=\"cl flag\"></i>Chile</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cn\"><i class=\"cn flag\"></i>China</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cx\"><i class=\"cx flag\"></i>Christmas Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cc\"><i class=\"cc flag\"></i>Cocos Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"co\"><i class=\"co flag\"></i>Colombia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"km\"><i class=\"km flag\"></i>Comoros</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cg\"><i class=\"cg flag\"></i>Congo Brazzaville</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cd\"><i class=\"cd flag\"></i>Congo</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ck\"><i class=\"ck flag\"></i>Cook Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cr\"><i class=\"cr flag\"></i>Costa Rica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ci\"><i class=\"ci flag\"></i>Cote Divoire</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hr\"><i class=\"hr flag\"></i>Croatia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cu\"><i class=\"cu flag\"></i>Cuba</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cy\"><i class=\"cy flag\"></i>Cyprus</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cz\"><i class=\"cz flag\"></i>Czech Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dk\"><i class=\"dk flag\"></i>Denmark</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dj\"><i class=\"dj flag\"></i>Djibouti</div>
\t\t\t\t\t<div class=\"item\" data-value=\"dm\"><i class=\"dm flag\"></i>Dominica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"do\"><i class=\"do flag\"></i>Dominican Republic</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ec\"><i class=\"ec flag\"></i>Ecuador</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eg\"><i class=\"eg flag\"></i>Egypt</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sv\"><i class=\"sv flag\"></i>El Salvador</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gb\"><i class=\"gb flag\"></i>England</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gq\"><i class=\"gq flag\"></i>Equatorial Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"er\"><i class=\"er flag\"></i>Eritrea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ee\"><i class=\"ee flag\"></i>Estonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"et\"><i class=\"et flag\"></i>Ethiopia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eu\"><i class=\"eu flag\"></i>European Union</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fk\"><i class=\"fk flag\"></i>Falkland Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fo\"><i class=\"fo flag\"></i>Faroe Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fj\"><i class=\"fj flag\"></i>Fiji</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fi\"><i class=\"fi flag\"></i>Finland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fr\"><i class=\"fr flag\"></i>France</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gf\"><i class=\"gf flag\"></i>French Guiana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pf\"><i class=\"pf flag\"></i>French Polynesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tf\"><i class=\"tf flag\"></i>French Territories</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ga\"><i class=\"ga flag\"></i>Gabon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gm\"><i class=\"gm flag\"></i>Gambia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ge\"><i class=\"ge flag\"></i>Georgia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"de\"><i class=\"de flag\"></i>Germany</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gh\"><i class=\"gh flag\"></i>Ghana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gi\"><i class=\"gi flag\"></i>Gibraltar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gr\"><i class=\"gr flag\"></i>Greece</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gl\"><i class=\"gl flag\"></i>Greenland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gd\"><i class=\"gd flag\"></i>Grenada</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gp\"><i class=\"gp flag\"></i>Guadeloupe</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gu\"><i class=\"gu flag\"></i>Guam</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gt\"><i class=\"gt flag\"></i>Guatemala</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gw\"><i class=\"gw flag\"></i>Guinea-Bissau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gn\"><i class=\"gn flag\"></i>Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gy\"><i class=\"gy flag\"></i>Guyana</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ht\"><i class=\"ht flag\"></i>Haiti</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hm\"><i class=\"hm flag\"></i>Heard Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hn\"><i class=\"hn flag\"></i>Honduras</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hk\"><i class=\"hk flag\"></i>Hong Kong</div>
\t\t\t\t\t<div class=\"item\" data-value=\"hu\"><i class=\"hu flag\"></i>Hungary</div>
\t\t\t\t\t<div class=\"item\" data-value=\"is\"><i class=\"is flag\"></i>Iceland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"in\"><i class=\"in flag\"></i>India</div>
\t\t\t\t\t<div class=\"item\" data-value=\"io\"><i class=\"io flag\"></i>Indian Ocean Territory</div>
\t\t\t\t\t<div class=\"item\" data-value=\"id\"><i class=\"id flag\"></i>Indonesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ir\"><i class=\"ir flag\"></i>Iran</div>
\t\t\t\t\t<div class=\"item\" data-value=\"iq\"><i class=\"iq flag\"></i>Iraq</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ie\"><i class=\"ie flag\"></i>Ireland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"il\"><i class=\"il flag\"></i>Israel</div>
\t\t\t\t\t<div class=\"item\" data-value=\"it\"><i class=\"it flag\"></i>Italy</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jm\"><i class=\"jm flag\"></i>Jamaica</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jp\"><i class=\"jp flag\"></i>Japan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"jo\"><i class=\"jo flag\"></i>Jordan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kz\"><i class=\"kz flag\"></i>Kazakhstan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ke\"><i class=\"ke flag\"></i>Kenya</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ki\"><i class=\"ki flag\"></i>Kiribati</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kw\"><i class=\"kw flag\"></i>Kuwait</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kg\"><i class=\"kg flag\"></i>Kyrgyzstan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"la\"><i class=\"la flag\"></i>Laos</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lv\"><i class=\"lv flag\"></i>Latvia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lb\"><i class=\"lb flag\"></i>Lebanon</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ls\"><i class=\"ls flag\"></i>Lesotho</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lr\"><i class=\"lr flag\"></i>Liberia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ly\"><i class=\"ly flag\"></i>Libya</div>
\t\t\t\t\t<div class=\"item\" data-value=\"li\"><i class=\"li flag\"></i>Liechtenstein</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lt\"><i class=\"lt flag\"></i>Lithuania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lu\"><i class=\"lu flag\"></i>Luxembourg</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mo\"><i class=\"mo flag\"></i>Macau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mk\"><i class=\"mk flag\"></i>Macedonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mg\"><i class=\"mg flag\"></i>Madagascar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mw\"><i class=\"mw flag\"></i>Malawi</div>
\t\t\t\t\t<div class=\"item\" data-value=\"my\"><i class=\"my flag\"></i>Malaysia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mv\"><i class=\"mv flag\"></i>Maldives</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ml\"><i class=\"ml flag\"></i>Mali</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mt\"><i class=\"mt flag\"></i>Malta</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mh\"><i class=\"mh flag\"></i>Marshall Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mq\"><i class=\"mq flag\"></i>Martinique</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mr\"><i class=\"mr flag\"></i>Mauritania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mu\"><i class=\"mu flag\"></i>Mauritius</div>
\t\t\t\t\t<div class=\"item\" data-value=\"yt\"><i class=\"yt flag\"></i>Mayotte</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mx\"><i class=\"mx flag\"></i>Mexico</div>
\t\t\t\t\t<div class=\"item\" data-value=\"fm\"><i class=\"fm flag\"></i>Micronesia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"md\"><i class=\"md flag\"></i>Moldova</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mc\"><i class=\"mc flag\"></i>Monaco</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mn\"><i class=\"mn flag\"></i>Mongolia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"me\"><i class=\"me flag\"></i>Montenegro</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ms\"><i class=\"ms flag\"></i>Montserrat</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ma\"><i class=\"ma flag\"></i>Morocco</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mz\"><i class=\"mz flag\"></i>Mozambique</div>
\t\t\t\t\t<div class=\"item\" data-value=\"na\"><i class=\"na flag\"></i>Namibia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nr\"><i class=\"nr flag\"></i>Nauru</div>
\t\t\t\t\t<div class=\"item\" data-value=\"np\"><i class=\"np flag\"></i>Nepal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"an\"><i class=\"an flag\"></i>Netherlands Antilles</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nl\"><i class=\"nl flag\"></i>Netherlands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nc\"><i class=\"nc flag\"></i>New Caledonia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pg\"><i class=\"pg flag\"></i>New Guinea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nz\"><i class=\"nz flag\"></i>New Zealand</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ni\"><i class=\"ni flag\"></i>Nicaragua</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ne\"><i class=\"ne flag\"></i>Niger</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ng\"><i class=\"ng flag\"></i>Nigeria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nu\"><i class=\"nu flag\"></i>Niue</div>
\t\t\t\t\t<div class=\"item\" data-value=\"nf\"><i class=\"nf flag\"></i>Norfolk Island</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kp\"><i class=\"kp flag\"></i>North Korea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"mp\"><i class=\"mp flag\"></i>Northern Mariana Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"no\"><i class=\"no flag\"></i>Norway</div>
\t\t\t\t\t<div class=\"item\" data-value=\"om\"><i class=\"om flag\"></i>Oman</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pk\"><i class=\"pk flag\"></i>Pakistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pw\"><i class=\"pw flag\"></i>Palau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ps\"><i class=\"ps flag\"></i>Palestine</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pa\"><i class=\"pa flag\"></i>Panama</div>
\t\t\t\t\t<div class=\"item\" data-value=\"py\"><i class=\"py flag\"></i>Paraguay</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pe\"><i class=\"pe flag\"></i>Peru</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ph\"><i class=\"ph flag\"></i>Philippines</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pn\"><i class=\"pn flag\"></i>Pitcairn Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pl\"><i class=\"pl flag\"></i>Poland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pt\"><i class=\"pt flag\"></i>Portugal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pr\"><i class=\"pr flag\"></i>Puerto Rico</div>
\t\t\t\t\t<div class=\"item\" data-value=\"qa\"><i class=\"qa flag\"></i>Qatar</div>
\t\t\t\t\t<div class=\"item\" data-value=\"re\"><i class=\"re flag\"></i>Reunion</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ro\"><i class=\"ro flag\"></i>Romania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ru\"><i class=\"ru flag\"></i>Russia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"rw\"><i class=\"rw flag\"></i>Rwanda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sh\"><i class=\"sh flag\"></i>Saint Helena</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kn\"><i class=\"kn flag\"></i>Saint Kitts and Nevis</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lc\"><i class=\"lc flag\"></i>Saint Lucia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"pm\"><i class=\"pm flag\"></i>Saint Pierre</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vc\"><i class=\"vc flag\"></i>Saint Vincent</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ws\"><i class=\"ws flag\"></i>Samoa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sm\"><i class=\"sm flag\"></i>San Marino</div>
\t\t\t\t\t<div class=\"item\" data-value=\"gs\"><i class=\"gs flag\"></i>Sandwich Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"st\"><i class=\"st flag\"></i>Sao Tome</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sa\"><i class=\"sa flag\"></i>Saudi Arabia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sn\"><i class=\"sn flag\"></i>Senegal</div>
\t\t\t\t\t<div class=\"item\" data-value=\"cs\"><i class=\"cs flag\"></i>Serbia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"rs\"><i class=\"rs flag\"></i>Serbia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sc\"><i class=\"sc flag\"></i>Seychelles</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sl\"><i class=\"sl flag\"></i>Sierra Leone</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sg\"><i class=\"sg flag\"></i>Singapore</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sk\"><i class=\"sk flag\"></i>Slovakia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"si\"><i class=\"si flag\"></i>Slovenia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sb\"><i class=\"sb flag\"></i>Solomon Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"so\"><i class=\"so flag\"></i>Somalia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"za\"><i class=\"za flag\"></i>South Africa</div>
\t\t\t\t\t<div class=\"item\" data-value=\"kr\"><i class=\"kr flag\"></i>South Korea</div>
\t\t\t\t\t<div class=\"item\" data-value=\"es\"><i class=\"es flag\"></i>Spain</div>
\t\t\t\t\t<div class=\"item\" data-value=\"lk\"><i class=\"lk flag\"></i>Sri Lanka</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sd\"><i class=\"sd flag\"></i>Sudan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sr\"><i class=\"sr flag\"></i>Suriname</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sj\"><i class=\"sj flag\"></i>Svalbard</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sz\"><i class=\"sz flag\"></i>Swaziland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"se\"><i class=\"se flag\"></i>Sweden</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ch\"><i class=\"ch flag\"></i>Switzerland</div>
\t\t\t\t\t<div class=\"item\" data-value=\"sy\"><i class=\"sy flag\"></i>Syria</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tw\"><i class=\"tw flag\"></i>Taiwan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tj\"><i class=\"tj flag\"></i>Tajikistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tz\"><i class=\"tz flag\"></i>Tanzania</div>
\t\t\t\t\t<div class=\"item\" data-value=\"th\"><i class=\"th flag\"></i>Thailand</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tl\"><i class=\"tl flag\"></i>Timorleste</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tg\"><i class=\"tg flag\"></i>Togo</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tk\"><i class=\"tk flag\"></i>Tokelau</div>
\t\t\t\t\t<div class=\"item\" data-value=\"to\"><i class=\"to flag\"></i>Tonga</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tt\"><i class=\"tt flag\"></i>Trinidad</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tn\"><i class=\"tn flag\"></i>Tunisia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tr\"><i class=\"tr flag\"></i>Turkey</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tm\"><i class=\"tm flag\"></i>Turkmenistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"tv\"><i class=\"tv flag\"></i>Tuvalu</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ug\"><i class=\"ug flag\"></i>Uganda</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ua\"><i class=\"ua flag\"></i>Ukraine</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ae\"><i class=\"ae flag\"></i>United Arab Emirates</div>
\t\t\t\t\t<div class=\"item\" data-value=\"us\"><i class=\"us flag\"></i>United States</div>
\t\t\t\t\t<div class=\"item\" data-value=\"uy\"><i class=\"uy flag\"></i>Uruguay</div>
\t\t\t\t\t<div class=\"item\" data-value=\"um\"><i class=\"um flag\"></i>Us Minor Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vi\"><i class=\"vi flag\"></i>Us Virgin Islands</div>
\t\t\t\t\t<div class=\"item\" data-value=\"uz\"><i class=\"uz flag\"></i>Uzbekistan</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vu\"><i class=\"vu flag\"></i>Vanuatu</div>
\t\t\t\t\t<div class=\"item\" data-value=\"va\"><i class=\"va flag\"></i>Vatican City</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ve\"><i class=\"ve flag\"></i>Venezuela</div>
\t\t\t\t\t<div class=\"item\" data-value=\"vn\"><i class=\"vn flag\"></i>Vietnam</div>
\t\t\t\t\t<div class=\"item\" data-value=\"wf\"><i class=\"wf flag\"></i>Wallis and Futuna</div>
\t\t\t\t\t<div class=\"item\" data-value=\"eh\"><i class=\"eh flag\"></i>Western Sahara</div>
\t\t\t\t\t<div class=\"item\" data-value=\"ye\"><i class=\"ye flag\"></i>Yemen</div>
\t\t\t\t\t<div class=\"item\" data-value=\"zm\"><i class=\"zm flag\"></i>Zambia</div>
\t\t\t\t\t<div class=\"item\" data-value=\"zw\"><i class=\"zw flag\"></i>Zimbabwe</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<h4 class=\"ui dividing header\">Billing Information</h4>
\t<div class=\"field\">
\t\t<label>Card Type</label>
\t\t<div class=\"ui selection dropdown\">
\t\t\t<input type=\"hidden\" name=\"card[type]\">
\t\t\t<div class=\"default text\">Type</div>
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t<div class=\"menu\">
\t\t\t\t<div class=\"item\" data-value=\"visa\">
\t\t\t\t\t<i class=\"visa icon\"></i>
\t\t\t\t\tVisa
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"amex\">
\t\t\t\t\t<i class=\"amex icon\"></i>
\t\t\t\t\tAmerican Express
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"discover\">
\t\t\t\t\t<i class=\"discover icon\"></i>
\t\t\t\t\tDiscover
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"fields\">
\t\t<div class=\"seven wide field\">
\t\t\t<label>Card Number</label>
\t\t\t<input type=\"text\" name=\"card[number]\" maxlength=\"16\" placeholder=\"Card #\">
\t\t</div>
\t\t<div class=\"three wide field\">
\t\t\t<label>CVC</label>
\t\t\t<input type=\"text\" name=\"card[cvc]\" maxlength=\"3\" placeholder=\"CVC\">
\t\t</div>
\t\t<div class=\"six wide field\">
\t\t\t<label>Expiration</label>
\t\t\t<div class=\"two fields\">
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<select class=\"ui fluid search dropdown\" name=\"card[expire-month]\">
\t\t\t\t\t\t<option value=\"\">Month</option>
\t\t\t\t\t\t<option value=\"1\">January</option>
\t\t\t\t\t\t<option value=\"2\">February</option>
\t\t\t\t\t\t<option value=\"3\">March</option>
\t\t\t\t\t\t<option value=\"4\">April</option>
\t\t\t\t\t\t<option value=\"5\">May</option>
\t\t\t\t\t\t<option value=\"6\">June</option>
\t\t\t\t\t\t<option value=\"7\">July</option>
\t\t\t\t\t\t<option value=\"8\">August</option>
\t\t\t\t\t\t<option value=\"9\">September</option>
\t\t\t\t\t\t<option value=\"10\">October</option>
\t\t\t\t\t\t<option value=\"11\">November</option>
\t\t\t\t\t\t<option value=\"12\">December</option>
\t\t\t\t\t</select>
\t\t\t\t</div>
\t\t\t\t<div class=\"field\">
\t\t\t\t\t<input type=\"text\" name=\"card[expire-year]\" maxlength=\"4\" placeholder=\"Year\">
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<h4 class=\"ui dividing header\">Receipt</h4>
\t<div class=\"field\">
\t\t<label>Send Receipt To:</label>
\t\t<div class=\"ui fluid multiple search selection dropdown\">
\t\t\t<input type=\"hidden\" name=\"receipt\">
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t<div class=\"default text\">Saved Contacts</div>
\t\t\t<div class=\"menu\">
\t\t\t\t<div class=\"item\" data-value=\"jenny\" data-text=\"Jenny\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/jenny.jpg\">
\t\t\t\t\tJenny Hess
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"elliot\" data-text=\"Elliot\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/elliot.jpg\">
\t\t\t\t\tElliot Fu
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"stevie\" data-text=\"Stevie\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/stevie.jpg\">
\t\t\t\t\tStevie Feliciano
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"christian\" data-text=\"Christian\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/christian.jpg\">
\t\t\t\t\tChristian
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"matt\" data-text=\"Matt\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/matt.jpg\">
\t\t\t\t\tMatt
\t\t\t\t</div>
\t\t\t\t<div class=\"item\" data-value=\"justen\" data-text=\"Justen\">
\t\t\t\t\t<img class=\"ui mini avatar image\" src=\"/images/avatar/small/justen.jpg\">
\t\t\t\t\tJusten Kitsune
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"ui segment\">
\t\t<div class=\"field\">
\t\t\t<div class=\"ui toggle checkbox\">
\t\t\t\t<input type=\"checkbox\" name=\"gift\" tabindex=\"0\" class=\"hidden\">
\t\t\t\t<label>Do not include a receipt in the package</label>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"ui button\" tabindex=\"0\">Submit Order</div>
</form>
{% endblock %}", "main.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\main.html");
    }
}
