<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sqds', function (Blueprint $table) {
            $table->id();
            // office_id
            $table->foreignId('office_id')->nullable()->constrained('offices');
            // language
            $table->string('language');
            $table->boolean('is_onsite')->default(true);
            // text header
            $table->text('header');
            // client_type
            $table->string('client_type');
            // citizen
            $table->string('citizen');
            // business
            $table->string('business');
            // government
            $table->string('government');
            // date
            $table->string('date');
            // sex
            $table->string('sex');
            $table->string('male');
            $table->string('female');
            $table->string('age');
            $table->string('region');

            // service_availed
            $table->string('service_availed');

            // instruction
            $table->text('cc_instruction');
            $table->text('sqd_instruction');

            $table->text('office_transacted');
            $table->text('service_availed_header');

            $table->text('label_sd');
            $table->text('label_d');
            $table->text('label_n');
            $table->text('label_a');
            $table->text('label_sa');
            $table->text('label_na');


            // office_transacted



            // sqd0 text to sqd8
            $table->text('sqd0');
            $table->text('sqd1');
            $table->text('sqd2');
            $table->text('sqd3');
            $table->text('sqd4');
            $table->text('sqd5');
            $table->text('sqd6');
            $table->text('sqd7');
            $table->text('sqd8');
            // cc1
            $table->text('cc1');
            // cc1_1 to cc1_4
            $table->text('cc1_1');
            $table->text('cc1_2');
            $table->text('cc1_3');
            $table->text('cc1_4');
            // cc2
            $table->text('cc2');
            // cc2_1 to cc2_5
            $table->text('cc2_1');
            $table->text('cc2_2');
            $table->text('cc2_3');
            $table->text('cc2_4');
            $table->text('cc2_5');
            // cc3
            $table->text('cc3');
            // cc3_1 to cc3_4
            $table->text('cc3_1');
            $table->text('cc3_2');
            $table->text('cc3_3');
            $table->text('cc3_4');

            // suggestion
            $table->text('suggestion');
            // email_address
            $table->string('email_address');

            // next string
            $table->string('next');
            // previous string
            $table->string('previous');

            $table->timestamps();
        });

        // insert english data onsite survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'english',
                'is_onsite' => true,
                'header' => 'This Client Satisfcation Measurement (CSM) tracks the customer experience of government offices. Your feedback on your recently concluded transaction will help this office provide a better service. Personal information shared will be kept confidential and you always have the option to not answer this form.',
                'client_type' => 'Select Client Type',
                'citizen' => 'Citizen',
                'business' => 'Business',
                'government' => 'Government (Employee or another agency)',
                'date' => 'Date',
                'sex' => 'Sex',
                'male' => 'Male',
                'female' => 'Female',
                'age' => 'Age',
                'region' => 'Region of Residence',

                'office_transacted' => 'Select Office you have transacted.',
                'service_availed_header' => 'Service you have availed.',

                'label_sd' => 'Strongly Disagree',
                'label_d' => 'Disagree',
                'label_n' => 'Neutral',
                'label_a' => 'Agree',
                'label_sa' => 'Strongly Agree',
                'label_na' => 'N/A',

                'service_availed' => 'Service Availed',

                'cc_instruction' => 'INSTRUCTIONS: Select your answer to the Citizen\'s Charter (CC) question. The Citizen\'s Charter is an official document that reflects the services of a government agency/office including its requirements, fees, and processing times amont others.',

                'sqd_instruction' => 'INSTRUCTIONS: For SQD 0-8, please select the emoji that best corresponds to your answer.',

                'sqd0' => 'SQD0. I am satisfied with the service that I availed.',
                'sqd1' => 'SQD1. I spent a reasonable amount of time for my transaction.',
                'sqd2' => 'SQD2. The office followed the transaction\'s requirements and steps based on the information provided.',
                'sqd3' => 'SQD3. The steps(including payment) I needed to do for my transaction were easy and simple.',
                'sqd4' => 'SQD4. I easily found information about my transaction from the office or its website.',
                'sqd5' => 'SQD5. I paid a resonable amount of fees for my transaction.',
                'sqd6' => 'SQD6. I feel the office was fair to everyone, or "walang palakasan", during my transaction.',
                'sqd7' => 'SQD7. I was treated courteously by the staff, and (if asked for help) the staff was helpful.',
                'sqd8' => 'SQD8. I got what I needed from the government ',

                'cc1' => 'CC1. Which of the following best describes your awareness of a CC?',
                'cc1_1' => 'I know what a CC is and I saw this office\'s CC.',
                'cc1_2' => 'I know what a CC is but I did not see this office\'s CC.',
                'cc1_3' => 'I learned of the CC only when I saw this office\'s CC.',
                'cc1_4' => 'I do not know what a CC is and I did not see one in this office.',

                'cc2' => 'CC2. If aware of CC, would you say that the CC of this office was...?',
                'cc2_1' => 'Easy to see',
                'cc2_2' => 'Somewhat easy to see',
                'cc2_3' => 'Difficult to see',
                'cc2_4' => 'Not visible at all',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. If aware of CC, how much did the CC help you in your transaction?',
                'cc3_1' => 'Helped very much',
                'cc3_2' => 'Somewhat helped',
                'cc3_3' => 'Did not help',
                'cc3_4' => 'N/A',

                'suggestion' => 'Suggestions on how we can further improve our services (optional)',
                'email_address' => 'Email Address (optional)',

                'next' => 'Next >>>',
                'previous' => '<<< Prev',
            ]
        );

        //   // insert english data online survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'english',
                'is_onsite' => false,

                'header' => 'This Client Satisfcation Measurement (CSM) tracks the customer experience of government offices. Your feedback on your recently concluded transaction will help this office provide a better service. Personal information shared will be kept confidential and you always have the option to not answer this form.',
                'client_type' => 'Select Client Type',
                'citizen' => 'Citizen',
                'business' => 'Business',
                'government' => 'Government (Employee or another agency)',
                'date' => 'Date',
                'sex' => 'Sex',
                'male' => 'Male',
                'female' => 'Female',
                'age' => 'Age',
                'region' => 'Region of Residence',

                'office_transacted' => 'Select Office you have transacted.',
                'service_availed_header' => 'Service you have availed.',

                'label_sd' => 'Strongly Disagree',
                'label_d' => 'Disagree',
                'label_n' => 'Neutral',
                'label_a' => 'Agree',
                'label_sa' => 'Strongly Agree',
                'label_na' => 'N/A',

                'service_availed' => 'Service Availed',

                'cc_instruction' => 'INSTRUCTIONS: Select your answer to the Citizen\'s Charter (CC) question. The Citizen\'s Charter is an official document that reflects the services of a government agency/office including its requirements, fees, and processing times amont others.',
                'sqd_instruction' => 'INSTRUCTIONS: For SQD 0-8, please select the emoji that best corresponds to your answer.',

                'sqd0' => 'SQD0. I am satisfied with the service that I availed.',
                'sqd1' => 'SQD1. I spent a reasonable amount of time for my transaction.',
                'sqd2' => 'SQD2. The office followed the transaction\'s requirements and steps based on the information provided.',
                'sqd3' => 'SQD3. The steps(including payment) I needed to do for my transaction were easy and simple.',
                'sqd4' => 'SQD4. I easily found information about my transaction from the office\'s website.',
                'sqd5' => 'SQD5. I paid a resonable amount of fees for my transaction.',
                'sqd6' => 'SQD6. I am confident my online transaction was secure.',
                'sqd7' => 'SQD7. The office\'s online support was available, and (if asked questions) online support was quick to respond.',
                'sqd8' => 'SQD8. I got what I needed from the government offic, or (if denied) denial of request was sufficiently explained to me.',

                'cc1' => 'CC1. Which of the following best describes your awareness of a CC?',
                'cc1_1' => 'I know what a CC is and I saw this office\'s CC.',
                'cc1_2' => 'I know what a CC is but I did not see this office\'s CC.',
                'cc1_3' => 'I learned of the CC only when I saw this office\'s CC.',
                'cc1_4' => 'I do not know what a CC is and I did not see one in this office.',

                'cc2' => 'CC2. If aware of CC, would you say that the CC of this office was...?',
                'cc2_1' => 'Easy to see',
                'cc2_2' => 'Somewhat easy to see',
                'cc2_3' => 'Difficult to see',
                'cc2_4' => 'Not visible at all',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. If aware of CC, how much did the CC help you in your transaction?',
                'cc3_1' => 'Helped very much',
                'cc3_2' => 'Somewhat helped',
                'cc3_3' => 'Did not help',
                'cc3_4' => 'N/A',

                'suggestion' => 'Suggestions on how we can further improve our services (optional)',
                'email_address' => 'Email Address (optional)',


                'next' => 'Next >>>',
                'previous' => '<<< Prev',
            ]
        );

        // insert tagalog data onsite survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'tagalog',
                'is_onsite' => true,
                'header' => 'Ang Client Satisfaction Measurement (CSM) ay naglalayong masubaybayan ang karanasan ng taumbayan hinggil sa kanilang pakikitransaksyon sa mga tanggapan ng gobyerno. Makatutulong ang inyong kasagutan ukol sa inyong naging karanasan sa kakatapos lamang na transaction, upang mas mapabuti at lalong mapahusay ang aming serbisyo publiko. Ang personal na impormasyon na iyong ibabahagi ay mananatiling kumpidensyal. Maaari ring piliin na hindi sagutan ang sarbey na ito.',
                'client_type' => 'Pumili Uri ng Kliyente',
                'citizen' => 'Mamamayan',
                'business' => 'Negosyo',
                'government' => 'Gobyerno (Empleyado o Ibang Ahensya)',
                'date' => 'Petsa',
                'sex' => 'Kasarian',
                'male' => 'Lalaki',
                'female' => 'Babae',
                'age' => 'Edad',
                'region' => 'Rehiyon',

                'office_transacted' => 'Pumili ng opisina na iyong pinuntahan.',
                'service_availed_header' => 'Pumili ng serbisyong natanggap',

                'label_sd' => 'Lubos na Hindi Sang-ayon',
                'label_d' => 'Hindi Sang-ayon',
                'label_n' => 'Neutral',
                'label_a' => 'Sang-ayon',
                'label_sa' => 'Lubos na Sang-ayon',
                'label_na' => 'Hindi Aplikable',

                'service_availed' => 'Serbisyong Natanggap',

                'cc_instruction' => 'PANUTO: Pumili ng iyong sagot sa tanong tungkol sa Citizen\'s Charter (CC). Ang Citizen\'s Charter ay isang opisyal na dokumento na naglalarawan ng mga serbisyo ng isang tanggapan ng gobyerno kasama ang mga kinakailangang dokumento, bayad, at oras ng pagproseso sa iba pa.',
                'sqd_instruction' => 'PANUTO: Para sa SQD 0-8, pumili ng emoji na pinakaangkop sa iyong sagot.',

                'sqd0' => 'SQD0. Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan.',
                'sqd1' => 'SQD1. Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon.',
                'sqd2' => 'SQD2. Ang opisina ay sumusunod sa mga kinakailangan dokomento at mga hakbang batay sa impormasyong ibinigay.',
                'sqd3' => 'SQD3. Ang mga hakbangsa pagproseso, kasama na ang pagbayad ay madali at simple lamang.',
                'sqd4' => 'SQD4. Mabilis at madali akong nkahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito.',
                'sqd5' => 'SQD5. Nagbayad ako ng makatwirang halaga para sa aking transaksyon.',
                'sqd6' => 'SQD6. Pakiramdam ko ay patas ang opisina sa lahat, o "walang palakasan", sa aking transaksyon.',
                'sqd7' => 'SQD7. Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humihingi ng tulong) alam ko na sila ay handang tumulong sa akin.',
                'sqd8' => 'SQD8. Nakuha ko ang kinakailangan ko mula sa tanggapan ng gobyerno, kung tinaggihan man, ito ay sapat na ipinaliwanag sa akin.',

                'cc1' => 'CC1. Alin sa mga sumusunod ang naglalarawan ng iyong kaalaman sa CC?',
                'cc1_1' => 'Alam ko ang CC at nakita ko ito sa napuntahan opisina.',
                'cc1_2' => 'Alam ko ang CC pero hindi ko ito nakita sa napuntahan Opisina.',
                'cc1_3' => 'Nalaman ko ang CC nang makita ko ito sa napuntahang opisina.',
                'cc1_4' => 'Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahan opisina.',

                'cc2' => 'CC2. Kung alam ang CC, masasabi mo ba na ang CC nang napuntahan opisina ay...',
                'cc2_1' => 'Madaling makita',
                'cc2_2' => 'Medyo madaling makita',
                'cc2_3' => 'Mahirap makita',
                'cc2_4' => 'Hindi makita',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. Kung alam ang CC, gaano nakatulong ang CC sa transaksyon mo?',
                'cc3_1' => 'Sobrang nakatulong',
                'cc3_2' => 'Nakatulong naman',
                'cc3_3' => 'Hindi nakatulong',
                'cc3_4' => 'N/A',

                'suggestion' => 'Mga suhestiyon kung paano pa mapapabuti ang aming mga serbisyo (opsyonal)',
                'email_address' => 'Email Address (opsyonal)',


                'next' => 'Sunod >>>',
                'previous' => '<<< Balik',
            ]
        );

        // insert tagalog data online survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'tagalog',
                'is_onsite' => false,
                'header' => 'Ang Client Satisfaction Measurement (CSM) ay naglalayong masubaybayan ang karanasan ng taumbayan hinggil sa kanilang pakikitransaksyon sa mga tanggapan ng gobyerno. Makatutulong ang inyong kasagutan ukol sa inyong naging karanasan sa kakatapos lamang na transaction, upang mas mapabuti at lalong mapahusay ang aming serbisyo publiko. Ang personal na impormasyon na iyong ibabahagi ay mananatiling kumpidensyal. Maaari ring piliin na hindi sagutan ang sarbey na ito.',
                'client_type' => 'Pumili Uri ng Kliyente',
                'citizen' => 'Mamamayan',
                'business' => 'Negosyo',
                'government' => 'Gobyerno (Empleyado o Ibang Ahensya)',
                'date' => 'Petsa',
                'sex' => 'Kasarian',
                'male' => 'Lalaki',
                'female' => 'Babae',
                'age' => 'Edad',
                'region' => 'Rehiyon',

                'office_transacted' => 'Pumili ng opisina na iyong pinuntahan.',
                'service_availed_header' => 'Pumili ng serbisyong natanggap',

                'label_sd' => 'Lubos na Hindi Sang-ayon',
                'label_d' => 'Hindi Sang-ayon',
                'label_n' => 'Neutral',
                'label_a' => 'Sang-ayon',
                'label_sa' => 'Lubos na Sang-ayon',
                'label_na' => 'Hindi Aplikable',

                'service_availed' => 'Serbisyong Natanggap',

                'cc_instruction' => 'PANUTO: Pumili ng iyong sagot sa tanong tungkol sa Citizen\'s Charter (CC). Ang Citizen\'s Charter ay isang opisyal na dokumento na naglalarawan ng mga serbisyo ng isang tanggapan ng gobyerno kasama ang mga kinakailangang dokumento, bayad, at oras ng pagproseso sa iba pa.',
                'sqd_instruction' => 'PANUTO: Para sa SQD 0-8, pumili ng emoji na pinakaangkop sa iyong sagot.',

                'sqd0' => 'SQD0. Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan.',
                'sqd1' => 'SQD1. Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon.',
                'sqd2' => 'SQD2. Ang opisina ay sumusunod sa mga kinakailangan dokomento at mga hakbang batay sa impormasyong ibinigay.',
                'sqd3' => 'SQD3. Ang mga hakbangsa pagproseso, kasama na ang pagbayad ay madali at simple lamang.',
                'sqd4' => 'SQD4. Mabilis at madali akong nkahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito.',
                'sqd5' => 'SQD5. Nagbayad ako ng makatwirang halaga para sa aking transaksyon.',
                'sqd6' => 'SQD6. Pakiramdam ko ay patas ang opisina sa lahat, o "walang palakasan", sa aking transaksyon.',
                'sqd7' => 'SQD7. Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humihingi ng tulong) alam ko na sila ay handang tumulong sa akin.',
                'sqd8' => 'SQD8. Nakuha ko ang kinakailangan ko mula sa tanggapan ng gobyerno, kung tinaggihan man, ito ay sapat na ipinaliwanag sa akin.',

                'cc1' => 'CC1. Alin sa mga sumusunod ang naglalarawan ng iyong kaalaman sa CC?',
                'cc1_1' => 'Alam ko ang CC at nakita ko ito sa napuntahan opisina.',
                'cc1_2' => 'Alam ko ang CC pero hindi ko ito nakita sa napuntahan Opisina.',
                'cc1_3' => 'Nalaman ko ang CC nang makita ko ito sa napuntahang opisina.',
                'cc1_4' => 'Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahan opisina.',

                'cc2' => 'CC2. Kung alam ang CC, masasabi mo ba na ang CC nang napuntahan opisina ay...',
                'cc2_1' => 'Madaling makita',
                'cc2_2' => 'Medyo madaling makita',
                'cc2_3' => 'Mahirap makita',
                'cc2_4' => 'Hindi makita',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. Kung alam ang CC, gaano nakatulong ang CC sa transaksyon mo?',
                'cc3_1' => 'Sobrang nakatulong',
                'cc3_2' => 'Nakatulong naman',
                'cc3_3' => 'Hindi nakatulong',
                'cc3_4' => 'N/A',

                'suggestion' => 'Mga suhestiyon kung paano pa mapapabuti ang aming mga serbisyo (opsyonal)',
                'email_address' => 'Email Address (opsyonal)',

                'next' => 'Sunod >>>',
                'previous' => '<<< Balik',
            ]
        );

        // insert bisaya data onsite survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'bisaya',
                'is_onsite' => true,
                'header' => 'Ang Client Satisfaction Measurement (CSM) nagtinguha nga masubay ang kasinatian sa katawhan bahin sa ilang transaksyon sa mga opisina sa gobyerno. Ang imong tubag bahin sa imong bag-ohay lang nahuman nga transaksyon makatabang aron mapalambo ug mapaayo pa ang among serbisyo publiko. Ang personal nga impormasyon nga imong ipaambit magpabiling kumpidensyal. Mahimo nimo nga dili tubagon ang kini nga sarbey.',
                'client_type' => 'Pili sa Klase sa Kliyente',
                'citizen' => 'Lungsuranon',
                'business' => 'Negosyo',
                'government' => 'Gobyerno (Empleyado o Laing Ahensya)',
                'date' => 'Petsa',
                'sex' => 'Gender',
                'male' => 'Lalaki',
                'female' => 'Babae',
                'age' => 'Edad',
                'region' => 'Rehiyon',

                'office_transacted' => 'Pili-a ang opisina nga imong gi bisita.',
                'service_availed_header' => 'Pilia ang serbisyo nga imong nadawat.',

                'label_sd' => 'Kusog nga Dili Uyon',
                'label_d' => 'Dili Uyon',
                'label_n' => 'Neutral',
                'label_a' => 'Uyon',
                'label_sa' => 'Kusog nga Uyon',
                'label_na' => 'Dili Aplikable',

                'service_availed' => 'Serbisyong Nadawat',

                'cc_instruction' => 'PANUTO: Pagpili sa imong tubag sa pangutana bahin sa Citizen\'s Charter (CC). Ang Citizen\'s Charter usa ka opisyal nga dokumento nga naghulagway sa mga serbisyo sa usa ka opisina sa gobyerno, lakip na ang mga kinahanglanon nga dokumento, bayad, ug oras sa pagproseso ug uban pa.',
                'sqd_instruction' => 'PANUTO: Para sa SQD 0-8, pagpili og emoji nga pinaka-angay sa imong tubag.',

                'sqd0' => 'SQD0. Nasatisfy ko sa serbisyo nga akong nadawat sa opisina nga akong nadtoan.',
                'sqd1' => 'SQD1. Makatarunganon ang oras nga akong gigugol para sa pagproseso sa akong transaksyon.',
                'sqd2' => 'SQD2. Ang opisina nagsunod sa mga kinahanglan nga dokumento ug mga lakang base sa impormasyon nga gihatag.',
                'sqd3' => 'SQD3. Ang mga lakang sa pagproseso, lakip na ang pagbayad, sayon ra ug yano lamang.',
                'sqd4' => 'SQD4. Pas-pas ug sayon ra nako nakuha ang impormasyon bahin sa akong transaksyon gikan sa opisina o sa website niini.',
                'sqd5' => 'SQD5. Nagbayad ko og makatwirang kantidad para sa akong transaksyon.',
                'sqd6' => 'SQD6. Gibati nako nga patas ang opisina sa tanan, o "walay palakasan", sa akong transaksyon.',
                'sqd7' => 'SQD7. Magalang ko nga giatiman sa mga kawani, ug (kung ako nangayo og tabang) akong nasayran nga andam silang motabang kanako.',
                'sqd8' => 'SQD8. Nakuha nako ang akong kinahanglan gikan sa opisina sa gobyerno, ug kung gibaliwala man, kini gihatagan ko og igo nga pasabot.',

                'cc1' => 'CC1. Unsa sa mosunod ang naghulagway sa imong kahibalo sa CC?',
                'cc1_1' => 'Nakahibalo ko sa CC ug nakita nako kini sa opisina nga akong nadtoan.',
                'cc1_2' => 'Nakahibalo ko sa CC apan wala ko kini nakita sa opisina nga akong nadtoan.',
                'cc1_3' => 'Nakadungog ko bahin sa CC human ko kini makita sa opisina nga akong nadtoan.',
                'cc1_4' => 'Wala ko kahibalo unsa ang CC ug wala ko kini nakita sa opisina nga akong nadtoan.',

                'cc2' => 'CC2. Kung nakahibalo sa CC, masulti ba nimo nga ang CC sa opisina nga imong nadtoan kay...',
                'cc2_1' => 'Sayon makita',
                'cc2_2' => 'Medyo sayon makita',
                'cc2_3' => 'Lisud makita',
                'cc2_4' => 'Dili makita',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. Kung nakahibalo sa CC, unsa ka nakatabang ang CC sa imong transaksyon?',
                'cc3_1' => 'Dakong natabang',
                'cc3_2' => 'Nakatabang ra man',
                'cc3_3' => 'Walay natabang',
                'cc3_4' => 'N/A',

                'suggestion' => 'Mga sugyot kung unsaon pa mapalambo ang among serbisyo (opsyonal)',
                'email_address' => 'Email Address (opsyonal)',

                'next' => 'Sunod >>>',
                'previous' => '<<< Balik',
            ]
        );

        // insert bisaya data online survey
        DB::table('sqds')->insert(
            [
                'office_id' => null,
                'language' => 'bisaya',
                'is_onsite' => false,
                'header' => 'Ang Client Satisfaction Measurement (CSM) nagtinguha nga masubay ang kasinatian sa katawhan bahin sa ilang transaksyon sa mga opisina sa gobyerno. Ang imong tubag bahin sa imong bag-ohay lang nahuman nga transaksyon makatabang aron mapalambo ug mapaayo pa ang among serbisyo publiko. Ang personal nga impormasyon nga imong ipaambit magpabiling kumpidensyal. Mahimo nimo nga dili tubagon ang kini nga sarbey.',
                'client_type' => 'Pili sa Klase sa Kliyente',
                'citizen' => 'Lungsuranon',
                'business' => 'Negosyo',
                'government' => 'Gobyerno (Empleyado o Laing Ahensya)',
                'date' => 'Petsa',
                'sex' => 'Gender',
                'male' => 'Lalaki',
                'female' => 'Babae',
                'age' => 'Edad',
                'region' => 'Rehiyon',

                'office_transacted' => 'Pili-a ang opisina nga imong gi bisita.',
                'service_availed_header' => 'Pilia ang serbisyo nga imong nadawat.',

                'label_sd' => 'Kusog nga Dili Uyon',
                'label_d' => 'Dili Uyon',
                'label_n' => 'Neutral',
                'label_a' => 'Uyon',
                'label_sa' => 'Kusog nga Uyon',
                'label_na' => 'Dili Aplikable',

                'service_availed' => 'Serbisyong Nadawat',

                'cc_instruction' => 'PANUTO: Pagpili sa imong tubag sa pangutana bahin sa Citizen\'s Charter (CC). Ang Citizen\'s Charter usa ka opisyal nga dokumento nga naghulagway sa mga serbisyo sa usa ka opisina sa gobyerno, lakip na ang mga kinahanglanon nga dokumento, bayad, ug oras sa pagproseso ug uban pa.',
                'sqd_instruction' => 'PANUTO: Para sa SQD 0-8, pagpili og emoji nga pinaka-angay sa imong tubag.',

                'sqd0' => 'SQD0. Nasatisfy ko sa serbisyo nga akong nadawat sa opisina nga akong nadtoan.',
                'sqd1' => 'SQD1. Makatarunganon ang oras nga akong gigugol para sa pagproseso sa akong transaksyon.',
                'sqd2' => 'SQD2. Ang opisina nagsunod sa mga kinahanglan nga dokumento ug mga lakang base sa impormasyon nga gihatag.',
                'sqd3' => 'SQD3. Ang mga lakang sa pagproseso, lakip na ang pagbayad, sayon ra ug yano lamang.',
                'sqd4' => 'SQD4. Pas-pas ug sayon ra nako nakuha ang impormasyon bahin sa akong transaksyon gikan sa opisina o sa website niini.',
                'sqd5' => 'SQD5. Nagbayad ko og makatwirang kantidad para sa akong transaksyon.',
                'sqd6' => 'SQD6. Gibati nako nga patas ang opisina sa tanan, o "walay palakasan", sa akong transaksyon.',
                'sqd7' => 'SQD7. Magalang ko nga giatiman sa mga kawani, ug (kung ako nangayo og tabang) akong nasayran nga andam silang motabang kanako.',
                'sqd8' => 'SQD8. Nakuha nako ang akong kinahanglan gikan sa opisina sa gobyerno, ug kung gibaliwala man, kini gihatagan ko og igo nga pasabot.',

                'cc1' => 'CC1. Unsa sa mosunod ang naghulagway sa imong kahibalo sa CC?',
                'cc1_1' => 'Nakahibalo ko sa CC ug nakita nako kini sa opisina nga akong nadtoan.',
                'cc1_2' => 'Nakahibalo ko sa CC apan wala ko kini nakita sa opisina nga akong nadtoan.',
                'cc1_3' => 'Nakadungog ko bahin sa CC human ko kini makita sa opisina nga akong nadtoan.',
                'cc1_4' => 'Wala ko kahibalo unsa ang CC ug wala ko kini nakita sa opisina nga akong nadtoan.',

                'cc2' => 'CC2. Kung nakahibalo sa CC, masulti ba nimo nga ang CC sa opisina nga imong nadtoan kay...',
                'cc2_1' => 'Sayon makita',
                'cc2_2' => 'Medyo sayon makita',
                'cc2_3' => 'Lisud makita',
                'cc2_4' => 'Dili makita',
                'cc2_5' => 'N/A',

                'cc3' => 'CC3. Kung nakahibalo sa CC, unsa ka nakatabang ang CC sa imong transaksyon?',
                'cc3_1' => 'Dakong natabang',
                'cc3_2' => 'Nakatabang ra man',
                'cc3_3' => 'Walay natabang',
                'cc3_4' => 'N/A',

                'suggestion' => 'Mga sugyot kung unsaon pa mapalambo ang among serbisyo (opsyonal)',
                'email_address' => 'Email Address (opsyonal)',

                'next' => 'Sunod >>>',
                'previous' => '<<< Balik',
            ]
        );
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sqds');
    }
};
