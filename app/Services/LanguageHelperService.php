<?php

namespace App\Services;

class LanguageHelperService
{
    /**
     * Common Tanglish to Tamil dictionary for high-accuracy exact matches.
     */
    protected static array $tanglishToTamil = [
        'naan' => 'நான்',
        'nan' => 'நான்',
        'unga' => 'உங்கள்',
        'ungala' => 'உங்களை',
        'unka' => 'உங்கள்',
        'unkala' => 'உங்களை',
        'romba' => 'ரொம்ப',
        'rasichen' => 'ரசிச்சேன்',
        'rasithen' => 'ரசித்தேன்',
        'rasichan' => 'ரசித்தேன்',
        'nalla' => 'நல்லா',
        'nallah' => 'நல்லா',
        'ezhuthi' => 'எழுதி',
        'eluthi' => 'எழுதி',
        'irukkeenga' => 'இருக்கீங்க',
        'irukinga' => 'இருக்கீங்க',
        'irukenga' => 'இருக்கீங்க',
        'irukeenga' => 'இருக்கீங்க',
        'irukkiraarkal' => 'இருக்கிறார்கள்',
        'irukanga' => 'இருக்காங்க',
        'enakku' => 'எனக்கு',
        'enaku' => 'எனக்கு',
        'pidichirukku' => 'பிடிச்சிருக்கு',
        'pidichuruku' => 'பிடிச்சிருக்கு',
        'pidithirukkirathu' => 'பிடித்திருக்கிறது',
        'ithu' => 'இது',
        'athu' => 'அது',
        'ethu' => 'எது',
        'vanakkam' => 'வணக்கம்',
        'nandri' => 'நன்றி',
        'nanri' => 'நன்றி',
        'story' => 'கதை',
        'kathaingo' => 'கதைங்கோ',
        'kathai' => 'கதை',
        'semma' => 'செம',
        'super' => 'சூப்பர்',
        'arputham' => 'அற்புதம்',
        'arumai' => 'அருமை',
        'vaazhthukkal' => 'வாழ்த்துகள்',
        'valthukal' => 'வாழ்த்துகள்',
        'valthukkal' => 'வாழ்த்துகள்',
        'padaipugal' => 'படைப்புகள்',
        'padithen' => 'படித்தேன்',
        'padichen' => 'படிச்சேன்',
        'padika' => 'படிக்க',
        'padikka' => 'படிக்க',
        'ezhuthu' => 'எழுத்து',
        'eluthu' => 'எழுத்து',
        'ezhuthalar' => 'எழுத்தாளர்',
        'eluthalar' => 'எழுத்தாளர்',
        'pala' => 'பல',
        'sila' => 'சில',
        'amma' => 'அம்மா',
        'appa' => 'அப்பா',
        'veedu' => 'வீடு',
        'poda' => 'போடா',
        'vada' => 'வாடா',
        'nanba' => 'நண்பா',
        'illa' => 'இல்ல',
        'illai' => 'இல்லை',
        'anbulla' => 'அன்புள்ள',
        'katturai' => 'கட்டுரை',
        'kadhai' => 'கதை',
        'irukku' => 'இருக்கு',
        'iruku' => 'இருக்கு',
        'irukkum' => 'இருக்கும்',
        'irukum' => 'இருக்கும்',
        'solla' => 'சொல்ல',
        'varum' => 'வரும்',
        'panna' => 'பண்ண',
        'pannunga' => 'பண்ணுங்க',
        'seiya' => 'செய்ய',
        'theriyum' => 'தெரியும்',
        'theriyala' => 'தெரியல',
        'theriyathu' => 'தெரியாது',
        'avanga' => 'அவங்க',
        'nallathu' => 'நல்லது',
        'migavum' => 'மிகவும்',
        'athigam' => 'அதிகம்',
        'artical' => 'ஆர்டிக்கிள்',
        'adei' => 'அடேய்',
        'adaey' => 'அடேய்',
        'adey' => 'அதே',
        'adae' => 'அடே',
        'irukken' => 'இருக்கேன்',
        'irukkaen' => 'இருக்கேன்',
        'irukaen' => 'இருக்கேன்',
        'iruken' => 'இருக்கேன்',
        'indha' => 'இந்த',
        'inge' => 'இங்கே',
        'ivan' => 'இவன்',
        'aah' => 'ஆ',
        'kaadhal' => 'காதல்',
        'paavam' => 'பாவம்',
        'vaanga' => 'வாங்க',
        'nee' => 'நீ',
        'thee' => 'தீ',
        'ungal' => 'உங்கள்',
        'udan' => 'உடன்',
        'poo' => 'பூ',
        'ooru' => 'ஊர்',
        'enna' => 'என்ன',
        'enge' => 'எங்கே',
        'vaera' => 'வேற',
        'paeru' => 'பேரு',
        'paiyan' => 'பையன்',
        'vai' => 'வை',
        'pani' => 'பணி',
        'serndhu' => 'சேர்ந்து',
        'rendu' => 'இரண்டு',
        'oru' => 'ஒரு',
        'maram' => 'மரம்',
        'ondru' => 'ஒன்று',
        'poonga' => 'போங்க',
        'ponga' => 'போங்க',
        'auvai' => 'ஔவை',
        'avvai' => 'ஔவை',
        'kauravam' => 'கௌரவம்',
        'gauravam' => 'கௌரவம்',
        'maunam' => 'மௌனம்',
        'mounam' => 'மௌனம்',
        'fan' => 'ஃபேன்',
        'file' => 'ஃபைல்',
        'coffee' => 'காஃபி',
        'office' => 'ஆஃபிஸ்',
        'phone' => 'ஃபோன்',
        'japan' => 'ஜப்பான்',
        'jam' => 'ஜாம்',
        'judge' => 'ஜட்ஜ்',
        'ja' => 'ஜ',
        'za' => 'ஜ',
        'j' => 'ஜ',
        'zoya' => 'ஜோயா',
        'ahdhu' => 'அஃது',
        'akhdhu' => 'அஃது',
        'aqdhu' => 'அஃது',
        'ehgu' => 'எஃகு',
        'ekhgu' => 'எஃகு',
        'eqgu' => 'எஃகு',
        'ahrinai' => 'அஃறிணை',
        'akhrinai' => 'அஃறிணை',
        'aqrinai' => 'அஃறிணை',
        'q' => 'ஃ',
        'school' => 'ஸ்கூல்',
        'station' => 'ஸ்டேஷன்',
        'status' => 'ஸ்டேட்டஸ்',
        'system' => 'சிஸ்டம்',
        'sistam' => 'சிஸ்டம்',
        'sri' => 'ஸ்ரீ',
        'shri' => 'ஸ்ரீ',
        'synthesis' => 'சிந்தஸிஸ்',
        'sinthasis' => 'சிந்தஸிஸ்',
        'sinthesis' => 'சிந்தஸிஸ்',
        'poi' => 'போய்',

        'sa' => 'ஸ',
        's' => 'ஸ',
        'shiva' => 'சிவா',
        'shankar' => 'சங்கர்',
        'sha' => 'ஷ',
        'sh' => 'ஷ',
        'hotel' => 'ஹோட்டல்',
        'hello' => 'ஹலோ',
        'ha' => 'ஹ',
        'h' => 'ஹ',
        'sound' => 'சௌண்ட்',
        'mount' => 'மவுண்ட்',
        'house' => 'ஹவுஸ்',
        'mouse' => 'மவுஸ்',
        'found' => 'ஃபவுண்ட்',
        'round' => 'ரவுண்ட்',
        'ground' => 'கிரவுண்ட்',
        'pound' => 'பவுண்ட்',
        'cloud' => 'கிளவுட்',
        'about' => 'அபவுட்',
        'out' => 'அவுட்',
        'count' => 'கவுண்ட்',
        'facebook' => 'ஃபேஸ்புக்',
        'film' => 'ஃபிலிம்',
        'fashion' => 'ஃபேஷன்',
        'manjakattu' => 'மஞ்சக்காட்டு',
        'manjakkattu' => 'மஞ்சக்காட்டு',
        'manjakkaattu' => 'மஞ்சக்காட்டு',
        'maina' => 'மைனா',
        'mainaa' => 'மைனா',
        'manina' => 'மைனா',
        'maninaa' => 'மைனா',
        'ennaik' => 'என்னைக்',
        'ennai' => 'என்னை',
        'konji' => 'கொஞ்சி',
        'konjik' => 'கொஞ்சிக்',
        'konjip' => 'கொஞ்சிப்',
        'pona' => 'போன',
        'ponaa' => 'போனா',
        'mustafa' => 'முஸ்தஃபா',
        'mustafaa' => 'முஸ்தஃபா',
        'mustafah' => 'முஸ்தஃபா',
        'musthafaa' => 'முஸ்தஃபா',
        'musthafaah' => 'முஸ்தஃபா',
        'dont' => 'டோன்ட்',
        'vory' => 'வொரி',
        'tholan' => 'தோழன்',
        'thozhan' => 'தோழன்',
        'moolgaatha' => 'மூழ்காத',
        'moolgatha' => 'மூழ்காத',
        'moozhgaatha' => 'மூழ்காத',
        'moozhgaadha' => 'மூழ்காத',
        'moolgaada' => 'மூழ்காத',
        'moolkaadha' => 'மூழ்காத',
        'moolkaatha' => 'மூழ்காத',
        'moozhgatha' => 'மூழ்காத',
        'moozhgadha' => 'மூழ்காத',
        'moozhgada' => 'மூழ்காத',
        'moolgada' => 'மூழ்காத',
        'friendshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
        'frendshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
        'frandshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
        'friendship' => 'ஃப்ரெண்ட்ஷிப்',
        'frendship' => 'ஃப்ரெண்ட்ஷிப்',
        'frandship' => 'ஃப்ரெண்ட்ஷிப்',
        'kariveppila' => 'கறிவேப்பில',
        'kariveppilai' => 'கறிவேப்பிலை',
        'karivepila' => 'கறிவேப்பில',
        'karivepilai' => 'கறிவேப்பிலை',
        'veppila' => 'வேப்பில',
        'veppilai' => 'வேப்பிலை',
        'vepila' => 'வேப்பில',
        'vepilai' => 'வேப்பிலை',
        'pena' => 'பேனா',
        'penai' => 'பேனா',
        'paena' => 'பேனா',
        'paenai' => 'பேனா',
        'take' => 'டேக்',
        'tak' => 'டேக்',
        'it' => 'இட்',
        'nyayiru' => 'ஞாயிறு',
        'gnayiru' => 'ஞாயிறு',
        'nyaayiru' => 'ஞாயிறு',
        'gnaayiru' => 'ஞாயிறு',
        'gnyayiru' => 'ஞாயிறு',
        'gnyaayiru' => 'ஞாயிறு',
        'nyabagam' => 'ஞாபகம்',
        'gnabagam' => 'ஞாபகம்',
        'nyaabagam' => 'ஞாபகம்',
        'gnaabagam' => 'ஞாபகம்',
        'gnyabagam' => 'ஞாபகம்',
        'gnyaabagam' => 'ஞாபகம்',
        'nyanam' => 'ஞானம்',
        'gnanam' => 'ஞானம்',
        'nyaanam' => 'ஞானம்',
        'gnaanam' => 'ஞானம்',
        'gnyanam' => 'ஞானம்',
        'gnyaanam' => 'ஞானம்',
        'vingyaanam' => 'விஞ்ஞானம்',
        'vingyanam' => 'விஞ்ஞானம்',
        'vingnyaanam' => 'விஞ்ஞானம்',
        'vingnyanam' => 'விஞ்ஞானம்',
        'vignyaanam' => 'விஞ்ஞானம்',
        'vignanam' => 'விஞ்ஞானம்',
        'angyaanam' => 'அஞ்ஞானம்',
        'angyanam' => 'அஞ்ஞானம்',
        'angnyaanam' => 'அஞ்ஞானம்',
        'angnyanam' => 'அஞ்ஞானம்',
        'agnyaanam' => 'அஞ்ஞானம்',
        'agnanam' => 'அஞ்ஞானம்',
    ];

    /**
     * Common Tamil-script English words to English dictionary.
     */
    protected static array $tamilToEnglish = [
        'ஐ' => 'I',
        'லவ்' => 'love',
        'யூ' => 'you',
        'திஸ்' => 'this',
        'ஆர்டிக்கிள்' => 'article',
        'ஆர்டிகிள்' => 'article',
        'சூப்பர்' => 'super',
        'நைஸ்' => 'nice',
        'குட்' => 'good',
        'தேங்க்ஸ்' => 'thanks',
        'தேங்க்யூ' => 'thank you',
        'கம்மென்ட்' => 'comment',
        'போஸ்ட்' => 'post',
        'வெப்சைட்' => 'website',
        'லிங்க்' => 'link',
        'ரைட்டர்' => 'writer',
        'ஸ்டோரி' => 'story',
        'லைக்' => 'like',
        'ஆசம்' => 'awesome',
        'வெரி' => 'very',
    ];

    /**
     * Custom dictionary mapping for multi-candidate words (spoken priority & English loan words).
     */
    protected static array $customWordCandidates = [
        'article' => ['article', 'ஆர்டிக்கிள்', 'கட்டுரை'],
        'review' => ['review', 'விமர்சனம்'],
        'blog' => ['blog', 'பதிவு'],
        'post' => ['post', 'பதிவு'],
        'comment' => ['comment', 'கருத்து'],
        'super' => ['சூப்பர்', 'super'],
        'enjoy' => ['என்ஜாய்', 'எஞ்சாய்'],
        'panni' => ['பண்ணி', 'பன்னி'],
        'padichen' => ['படிச்சேன்', 'படித்தேன்'],
        'naan' => ['நான்'],
        'romba' => ['ரொம்ப'],
        'story' => ['கதை'],
        'kathaingo' => ['கதைங்கோ'],
        'kathai' => ['கதை'],
        'ezhuthi' => ['எழுதி'],
        'eluthi' => ['எழுதி'],
        'irukkeenga' => ['இருக்கீங்க'],
        'enakku' => ['எனக்கு'],
        'enaku' => ['எனக்கு'],
        'pidichirukku' => ['பிடிச்சிருக்கு'],
        'pidichuruku' => ['பிடிச்சிருக்கு'],
        'arputham' => ['அற்புதம்'],
        'arumai' => ['அருமை'],
        'vaazhthukkal' => ['வாழ்த்துகள்'],
        'padaipugal' => ['படைப்புகள்'],
        'padithen' => ['படித்தேன்'],
        'ezhuthalar' => ['எழுத்தாளர்'],
        'eluthalar' => ['எழுத்தாளர்'],
        'katturai' => ['கட்டுரை'],
        'kadhai' => ['கதை'],
        'rasichen' => ['ரசிச்சேன்', 'ரசித்தேன்'],
        'anbulla' => ['அன்புள்ள', 'அன்புல்ல', 'அன்புள்ளே', 'அன்புடன்'],
        'eluthalare' => ['எழுத்தாளரே', 'எழுதுபவரே'],
        'naam' => ['நாம்', 'நாம', 'நம'],
        'rendu' => ['இரண்டு', 'ரெண்டு'],
        'serndhu' => ['சேர்ந்து', 'செர்ந்து'],
        'illa' => ['இல்ல', 'இல்லை'],
        'illai' => ['இல்லை'],
        'irukku' => ['இருக்கு'],
        'solla' => ['சொல்ல'],
        'varum' => ['வரும்'],
        'panna' => ['பண்ண'],
        'seiya' => ['செய்ய'],
        'theriyum' => ['தெரியும்'],
        'avanga' => ['அவங்க'],
        'nallathu' => ['நல்லது'],
        'migavum' => ['மிகவும்'],
        'athigam' => ['அதிகம்'],
        'adei' => ['அடேய்', 'அடெய்', 'அடெஇ', 'அடே', 'அடை'],
        'adaey' => ['அடேய்', 'அடெய்', 'அடெஇ', 'அடே', 'அடை'],
        'adey' => ['அதே', 'அடே', 'அடேய்', 'அதேய்'],
        'adae' => ['அடே', 'அதே'],
        'irukken' => ['இருக்கேன்', 'இருக்கென்'],
        'irukkaen' => ['இருக்கேன்', 'இருக்கென்'],
        'irukaen' => ['இருக்கேன்', 'இருக்கென்'],
        'iruken' => ['இருக்கேன்', 'இருக்கென்'],
        'indha' => ['இந்த', 'இங்கு', 'இந்தா'],
        'inge' => ['இங்கே', 'இங்கெ', 'இங்கு'],
        'ivan' => ['இவன்'],
        'aah' => ['ஆ'],
        'kaadhal' => ['காதல்'],
        'paavam' => ['பாவம்'],
        'vaanga' => ['வாங்க', 'வாங்கு'],
        'nee' => ['நீ'],
        'thee' => ['தீ', 'தே'],
        'ungal' => ['உங்கள்', 'உங்க'],
        'udan' => ['உடன்', 'உடனே'],
        'poo' => ['பூ', 'பூக்கள்'],
        'ooru' => ['ஊர்', 'ஊரு'],
        'enna' => ['என்ன', 'ஏன்னா'],
        'enge' => ['எங்கே', 'எங்கெ', 'எங்கு'],
        'vaera' => ['வேற', 'வேறு', 'வேர'],
        'paeru' => ['பேரு', 'பேர்'],
        'paiyan' => ['பையன்', 'பையன்டா'],
        'vai' => ['வை', 'வைக்க'],
        'pani' => ['பணி', 'பனி'],
        'oru' => ['ஒரு', 'ஒரே'],
        'maram' => ['மரம்', 'மறம்'],
        'ondru' => ['ஒன்று', 'ஒன்னு'],
        'poonga' => ['போங்க', 'போங்கள்'],
        'ponga' => ['போங்க', 'பொங்க', 'பொங்கல்', 'போங்கள்'],
        'auvai' => ['ஔவை'],
        'avvai' => ['ஔவை'],
        'kauravam' => ['கௌரவம்'],
        'gauravam' => ['கௌரவம்'],
        'maunam' => ['மௌனம்', 'மோனம்', 'மொனம்'],
        'mounam' => ['மௌனம்', 'மோனம்', 'மொனம்'],
        'fan' => ['ஃபேன்', 'ஃபன்'],
        'file' => ['ஃபைல்', 'file'],
        'coffee' => ['காஃபி'],
        'office' => ['ஆஃபிஸ்'],
        'phone' => ['ஃபோன்', 'போன்'],
        'japan' => ['ஜப்பான்'],
        'jam' => ['ஜாம்'],
        'judge' => ['ஜட்ஜ்'],
        'ja' => ['ஜ'],
        'za' => ['ஜ'],
        'j' => ['ஜ'],
        'zoya' => ['ஜோயா'],
        'ahdhu' => ['அஃது'],
        'akhdhu' => ['அஃது'],
        'aqdhu' => ['அஃது'],
        'ehgu' => ['எஃகு'],
        'ekhgu' => ['எஃகு'],
        'eqgu' => ['எஃகு'],
        'ahrinai' => ['அஃறிணை'],
        'akhrinai' => ['அஃறிணை'],
        'aqrinai' => ['அஃறிணை'],
        'q' => ['ஃ'],
        'school' => ['ஸ்கூல்'],
        'station' => ['ஸ்டேஷன்'],
        'status' => ['ஸ்டேட்டஸ்'],
        'system' => ['சிஸ்டம்', 'system'],
        'sistam' => ['சிஸ்டம்'],
        'sri' => ['ஸ்ரீ'],
        'shri' => ['ஸ்ரீ'],
        'synthesis' => ['சிந்தஸிஸ்', 'சின்தஸிஸ்'],
        'sinthasis' => ['சிந்தஸிஸ்', 'சின்தஸிஸ்'],
        'sinthesis' => ['சிந்தஸிஸ்', 'சின்தஸிஸ்'],
        'poi' => ['போய்', 'பொய்', 'போயி'],
        'so' => ['சோ', 'சொ'],
        'no' => ['நோ', 'நொ'],
        'go' => ['கோ', 'கொ'],
        'do' => ['டோ', 'டொ'],
        'to' => ['டோ', 'டொ'],
        'sa' => ['ஸ'],


        's' => ['ஸ'],
        'shiva' => ['சிவா', 'ஷிவா'],
        'shankar' => ['சங்கர்', 'ஷங்கர்'],
        'sha' => ['ஷ', 'ச'],
        'sh' => ['ஷ', 'ஷ்', 'ச்'],
        'hotel' => ['ஹோட்டல்'],
        'hello' => ['ஹலோ'],
        'ha' => ['ஹ'],
        'h' => ['ஹ'],
        'sound' => ['சௌண்ட்', 'சவுண்ட்'],
        'mount' => ['மவுண்ட்', 'மௌண்ட்'],
        'house' => ['ஹவுஸ்', 'ஹௌஸ்'],
        'mouse' => ['மவுஸ்', 'மௌஸ்'],
        'found' => ['ஃபவுண்ட்', 'ஃபௌண்ட்', 'பவுண்ட்'],
        'round' => ['ரவுண்ட்', 'ரௌண்ட்'],
        'ground' => ['கிரவுண்ட்', 'கிரௌண்ட்'],
        'pound' => ['பவுண்ட்', 'பௌண்ட்'],
        'cloud' => ['கிளவுட்', 'கிளௌட்'],
        'about' => ['அபவுட்', 'அபௌட்'],
        'out' => ['அவுட்', 'அௌட்'],
        'count' => ['கவுண்ட்', 'கௌண்ட்'],
        'facebook' => ['ஃபேஸ்புக்'],
        'film' => ['ஃபிலிம்'],
        'fashion' => ['ஃபேஷன்'],
        'manjakattu' => ['மஞ்சக்காட்டு', 'மஞ்சக்கட்டு', 'மஞ்சகட்டு'],
        'manjakkattu' => ['மஞ்சக்காட்டு', 'மஞ்சக்கட்டு'],
        'manjakkaattu' => ['மஞ்சக்காட்டு'],
        'maina' => ['மைனா', 'மைநா'],
        'mainaa' => ['மைனா'],
        'manina' => ['மைனா', 'மைநா'],
        'maninaa' => ['மைனா'],
        'ennai' => ['என்னை', 'என்னைக்'],
        'ennaik' => ['என்னைக்', 'என்னை'],
        'konji' => ['கொஞ்சி', 'கொஞ்சிக்', 'கொஞ்சிப்'],
        'konjik' => ['கொஞ்சிக்', 'கொஞ்சி'],
        'konjip' => ['கொஞ்சிப்', 'கொஞ்சி'],
        'pona' => ['போன', 'போனா'],
        'ponaa' => ['போனா', 'போன'],
        'mustafa' => ['முஸ்தஃபா', 'முஸ்தபா'],
        'mustafaa' => ['முஸ்தஃபா', 'முஸ்தபா'],
        'mustafah' => ['முஸ்தஃபா', 'முஸ்தபா', 'முஸ்தபஹ்'],
        'musthafaa' => ['முஸ்தஃபா', 'முஸ்தபா'],
        'musthafaah' => ['முஸ்தஃபா', 'முஸ்தபா', 'முஸ்தபஹ்'],
        'dont' => ['டோன்ட்', 'டோண்ட்', 'டோன்டு'],
        'vory' => ['வொரி', 'வொறி'],
        'tholan' => ['தோழன்', 'தொழன்', 'தொலன்'],
        'thozhan' => ['தோழன்'],
        'moolgaatha' => ['மூழ்காத', 'மூல்காத'],
        'moolgatha' => ['மூழ்காத', 'மூல்காத'],
        'moozhgaatha' => ['மூழ்காத', 'மூல்காத'],
        'moozhgaadha' => ['மூழ்காத', 'மூல்காத'],
        'moolgaada' => ['மூழ்காத', 'மூல்காத'],
        'moolkaadha' => ['மூழ்காத', 'மூல்காத'],
        'moolkaatha' => ['மூழ்காத', 'மூல்காத'],
        'moozhgatha' => ['மூழ்காத', 'மூல்காத'],
        'moozhgadha' => ['மூழ்காத', 'மூல்காத'],
        'moozhgada' => ['மூழ்காத', 'மூல்காத'],
        'moolgada' => ['மூழ்காத', 'மூல்காத'],
        'friendshippaa' => ['ஃப்ரண்ட்ஷிப்பா', 'ஃப்ரெண்ட்ஷிப்பா', 'பிரண்ட்ஷிப்பா'],
        'frendshippaa' => ['ஃப்ரண்ட்ஷிப்பா', 'ஃப்ரெண்ட்ஷிப்பா', 'பிரண்ட்ஷிப்பா'],
        'frandshippaa' => ['ஃப்ரண்ட்ஷிப்பா', 'ஃப்ரெண்ட்ஷிப்பா', 'பிரண்ட்ஷிப்பா'],
        'friendship' => ['ஃப்ரெண்ட்ஷிப்', 'பிரண்ட்ஷிப்'],
        'frendship' => ['ஃப்ரெண்ட்ஷிப்', 'பிரண்ட்ஷிப்'],
        'frandship' => ['ஃப்ரெண்ட்ஷிப்', 'பிரண்ட்ஷிப்'],
        'kariveppila' => ['கறிவேப்பில', 'கறிவேப்பிலை'],
        'kariveppilai' => ['கறிவேப்பிலை', 'கறிவேப்பில'],
        'karivepila' => ['கறிவேப்பில', 'கறிவேப்பிலை'],
        'karivepilai' => ['கறிவேப்பிலை', 'கறிவேப்பில'],
        'veppila' => ['வேப்பில', 'வேப்பிலை'],
        'veppilai' => ['வேப்பிலை', 'வேப்பில'],
        'vepila' => ['வேப்பில', 'வேப்பிலை'],
        'vepilai' => ['வேப்பிலை', 'வேப்பில'],
        'pena' => ['பேனா', 'பேண', 'பென'],
        'penai' => ['பேனா', 'பேண'],
        'paena' => ['பேனா', 'பேண', 'பென'],
        'paenai' => ['பேனா', 'பேண'],
        'take' => ['டேக்', 'take'],
        'tak' => ['டேக்', 'டக்', 'tak'],
        'it' => ['இட்', 'it'],
        'nyayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'gnayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'nyaayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'gnaayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'gnyayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'gnyaayiru' => ['ஞாயிறு', 'ஞாயிரு'],
        'nyabagam' => ['ஞாபகம்'],
        'gnabagam' => ['ஞாபகம்'],
        'nyaabagam' => ['ஞாபகம்'],
        'gnaabagam' => ['ஞாபகம்'],
        'gnyabagam' => ['ஞாபகம்'],
        'gnyaabagam' => ['ஞாபகம்'],
        'nyanam' => ['ஞானம்'],
        'gnanam' => ['ஞானம்'],
        'nyaanam' => ['ஞானம்'],
        'gnaanam' => ['ஞானம்'],
        'gnyanam' => ['ஞானம்'],
        'gnyaanam' => ['ஞானம்'],
        'vingyaanam' => ['விஞ்ஞானம்', 'விஞ்ஞானம்'],
        'vingyanam' => ['விஞ்ஞானம்'],
        'vingnyaanam' => ['விஞ்ஞானம்'],
        'vingnyanam' => ['விஞ்ஞானம்'],
        'vignyaanam' => ['விஞ்ஞானம்'],
        'vignanam' => ['விஞ்ஞானம்'],
        'angyaanam' => ['அஞ்ஞானம்', 'அஞ்ஞானம்'],
        'angyanam' => ['அஞ்ஞானம்'],
        'angnyaanam' => ['அஞ்ஞானம்'],
        'angnyanam' => ['அஞ்ஞானம்'],
        'agnyaanam' => ['அஞ்ஞானம்'],
        'agnanam' => ['அஞ்ஞானம்'],
    ];


    /**

     * English words to preserve without transliterating.
     */
    protected static array $englishPreserved = [
        'a', 'able', 'about', 'above', 'act', 'add', 'after', 'again', 'against', 'ago', 'air', 'all',
        'also', 'always', 'am', 'among', 'an', 'and', 'animal', 'answer', 'any', 'appear', 'are', 'area',
        'arm', 'art', 'article', 'articles', 'as', 'ask', 'at', 'awesome', 'back', 'ball', 'base', 'be',
        'beauty', 'bed', 'been', 'before', 'began', 'begin', 'behind', 'believe', 'best', 'better', 'between', 'big',
        'bird', 'black', 'blog', 'blogs', 'blue', 'boat', 'body', 'book', 'both', 'box', 'boy', 'brilliant',
        'bring', 'brother', 'brought', 'build', 'busy', 'but', 'by', 'call', 'came', 'can', 'car', 'care',
        'carry', 'cause', 'cell', 'center', 'certain', 'change', 'check', 'children', 'circle', 'city', 'class', 'clear',
        'close', 'cold', 'color', 'come', 'comment', 'commented', 'commenting', 'comments', 'common', 'complete', 'contain', 'correct',
        'could', 'count', 'country', 'course', 'cover', 'cross', 'cry', 'cut', 'dance', 'dark', 'day', 'decide',
        'deep', 'develop', 'did', 'differ', 'difficult', 'direct', 'distant', 'divide', 'do', 'does', 'dog', 'don\'t',
        'done', 'door', 'down', 'draw', 'drive', 'drop', 'dry', 'during', 'each', 'early', 'earth', 'ease',
        'easily', 'east', 'easy', 'eat', 'egg', 'end', 'energy', 'engine', 'enjoy', 'enjoyable', 'enjoyed', 'enjoying',
        'enjoys', 'enough', 'equate', 'even', 'ever', 'every', 'example', 'excellent', 'eye', 'face', 'fact', 'fall',
        'family', 'far', 'farm', 'fast', 'father', 'feel', 'feeling', 'feelings', 'feet', 'felt', 'few', 'field',
        'figure', 'fill', 'final', 'find', 'fine', 'fire', 'first', 'fish', 'five', 'fly', 'follow', 'food',
        'foot', 'for', 'force', 'forest', 'form', 'found', 'four', 'fraction', 'free', 'friend', 'from', 'front',
        'full', 'game', 'gave', 'general', 'get', 'girl', 'give', 'go', 'gold', 'good', 'got', 'govern',
        'grand', 'great', 'green', 'ground', 'group', 'grow', 'had', 'half', 'hand', 'happen', 'happy', 'hard',
        'has', 'have', 'he', 'head', 'hear', 'heard', 'heart', 'heat', 'heavy', 'hello', 'help', 'her',
        'here', 'hey', 'hi', 'high', 'him', 'his', 'hold', 'home', 'horse', 'hot', 'hour', 'house',
        'how', 'hundred', 'hunt', 'i', 'ice', 'idea', 'if', 'in', 'inch', 'include', 'interest', 'is',
        'island', 'it', 'just', 'keep', 'kind', 'king', 'knew', 'know', 'land', 'language', 'large', 'last',
        'late', 'laugh', 'lay', 'lead', 'learn', 'leave', 'left', 'length', 'less', 'let', 'letter', 'life',
        'light', 'like', 'liked', 'likes', 'liking', 'line', 'list', 'listen', 'little', 'live', 'long', 'look',
        'love', 'loved', 'loves', 'loving', 'low', 'machine', 'made', 'main', 'make', 'man', 'many', 'map',
        'mark', 'material', 'matter', 'may', 'me', 'mean', 'measure', 'men', 'might', 'mile', 'mind', 'minute',
        'miss', 'money', 'moon', 'more', 'morning', 'most', 'mother', 'mountain', 'move', 'much', 'multiply', 'music',
        'must', 'my', 'name', 'near', 'need', 'never', 'new', 'next', 'nice', 'night', 'no', 'north',
        'note', 'nothing', 'notice', 'noun', 'now', 'number', 'numeral', 'object', 'ocean', 'of', 'off', 'often',
        'oh', 'ok', 'okay', 'old', 'on', 'once', 'one', 'only', 'open', 'or', 'order', 'other',
        'our', 'out', 'over', 'own', 'page', 'pages', 'paint', 'pair', 'paper', 'part', 'pass', 'pattern',
        'people', 'perhaps', 'person', 'pick', 'picture', 'piece', 'place', 'plain', 'plan', 'plane', 'plant', 'play',
        'please', 'pls', 'point', 'port', 'pose', 'position', 'possible', 'post', 'posted', 'posting', 'posts', 'pound',
        'power', 'present', 'press', 'probable', 'problem', 'produce', 'product', 'pull', 'put', 'question', 'quick', 'race',
        'rain', 'ran', 'reach', 'read', 'reader', 'readers', 'reading', 'reads', 'ready', 'real', 'reason', 'record',
        'red', 'region', 'remember', 'replied', 'replies', 'reply', 'replying', 'represent', 'rest', 'ride', 'right', 'river',
        'road', 'rock', 'room', 'round', 'rule', 'run', 's', 'sad', 'said', 'sail', 'same', 'saw',
        'say', 'school', 'science', 'sea', 'second', 'see', 'seem', 'self', 'semma', 'sentence', 'serve', 'set',
        'settle', 'several', 'shape', 'she', 'ship', 'short', 'should', 'show', 'side', 'simple', 'since', 'sing',
        'sit', 'site', 'six', 'size', 'slow', 'small', 'snow', 'so', 'some', 'song', 'soon', 'sorry',
        'sound', 'south', 'space', 'speak', 'special', 'spell', 'square', 'stand', 'star', 'start', 'state', 'stay',
        'stead', 'step', 'still', 'stood', 'stop', 'story', 'street', 'strong', 'struggle', 'struggled', 'struggling', 'study',
        'subject', 'such', 'sudden', 'sun', 'super', 'superb', 'sure', 'surface', 'syllable', 'system', 'table', 'tail',
        'take', 'talk', 'teach', 'tell', 'ten', 'test', 'than', 'thank', 'thanks', 'thankyou', 'that', 'the',
        'their', 'them', 'then', 'there', 'these', 'they', 'thing', 'think', 'this', 'those', 'though', 'thought',
        'thousand', 'three', 'through', 'time', 'tire', 'to', 'together', 'told', 'too', 'took', 'top', 'toward',
        'town', 'travel', 'tree', 'tried', 'true', 'try', 'trying', 'turn', 'two', 'under', 'unit', 'until',
        'up', 'us', 'use', 'usual', 'vary', 'verb', 'very', 'voice', 'vowel', 'wait', 'walk', 'want',
        'war', 'warm', 'was', 'watch', 'water', 'wave', 'way', 'we', 'website', 'week', 'weight', 'welcome',
        'well', 'went', 'were', 'west', 'what', 'wheel', 'when', 'where', 'which', 'while', 'white', 'who',
        'whole', 'why', 'wide', 'will', 'wind', 'window', 'with', 'wonder', 'wonderful', 'wood', 'word', 'work',
        'world', 'would', 'write', 'writer', 'writers', 'writes', 'writing', 'year', 'yes', 'yet', 'you', 'young',
        'your',
    ];

    protected static ?array $englishPreservedFlipped = null;

    protected function isEnglishPreserved(string $word): bool
    {
        if (self::$englishPreservedFlipped === null) {
            self::$englishPreservedFlipped = array_flip(self::$englishPreserved);
        }
        return isset(self::$englishPreservedFlipped[$word]);
    }

    /**
     * Phonetic consonants mapping.
     */
    protected static array $consonants = [
        'ng' => ['dot' => 'ங்', 'base' => 'ங்க'],
        'nj' => ['dot' => 'ஞ்', 'base' => 'ஞ்ச'],
        'ngny' => ['dot' => 'ஞ்', 'base' => 'ஞ்ஞ'],
        'ngy' => ['dot' => 'ஞ்', 'base' => 'ஞ்ஞ'],
        'gny' => ['dot' => 'ஞ்', 'base' => 'ஞ'],
        'ny' => ['dot' => 'ஞ்', 'base' => 'ஞ'],
        'gn' => ['dot' => 'ஞ்', 'base' => 'ஞ'],
        'ndr' => ['dot' => 'ன்ற்', 'base' => 'ன்ற'],
        'ndh' => ['dot' => 'ந்த்', 'base' => 'ந்த'],
        'nth' => ['dot' => 'ந்த்', 'base' => 'ந்த'],
        'ksh' => ['dot' => 'க்ஷ்', 'base' => 'க்ஷ'],
        'nd' => ['dot' => 'ந்த்', 'base' => 'ந்த'],

        'th' => ['dot' => 'த்', 'base' => 'த'],
        'zh' => ['dot' => 'ழ்', 'base' => 'ழ'],
        'sh' => ['dot' => 'ஷ்', 'base' => 'ஷ'],
        'ch' => ['dot' => 'ச்', 'base' => 'ச'],
        'kh' => ['dot' => 'க்', 'base' => 'க'],
        'ph' => ['dot' => 'ஃப்', 'base' => 'ஃப'],
        'gh' => ['dot' => 'க்', 'base' => 'க'],
        'lh' => ['dot' => 'ள்', 'base' => 'ள'],
        'dh' => ['dot' => 'த்', 'base' => 'த'],
        'k' => ['dot' => 'க்', 'base' => 'க'],
        'g' => ['dot' => 'க்', 'base' => 'க'],
        'c' => ['dot' => 'ச்', 'base' => 'ச'],
        's' => ['dot' => 'ச்', 'base' => 'ச'],
        'j' => ['dot' => 'ஜ்', 'base' => 'ஜ'],
        't' => ['dot' => 'ட்', 'base' => 'ட'],
        'd' => ['dot' => 'ட்', 'base' => 'ட'],
        'n' => ['dot' => 'ன்', 'base' => 'ன'], // handled in code for initial 'n' -> 'ந'
        'p' => ['dot' => 'ப்', 'base' => 'ப'],
        'b' => ['dot' => 'ப்', 'base' => 'ப'],
        'f' => ['dot' => 'ஃப்', 'base' => 'ஃப'],
        'm' => ['dot' => 'ம்', 'base' => 'ம'],
        'y' => ['dot' => 'ய்', 'base' => 'ய'],
        'r' => ['dot' => 'ர்', 'base' => 'ர'],
        'l' => ['dot' => 'ல்', 'base' => 'ல'],
        'v' => ['dot' => 'வ்', 'base' => 'வ'],
        'w' => ['dot' => 'வ்', 'base' => 'வ'],
        'h' => ['dot' => 'ஹ்', 'base' => 'ஹ'],
        'z' => ['dot' => 'ஜ்', 'base' => 'ஜ'],
        'q' => ['dot' => 'ஃ', 'base' => 'ஃ'],
    ];

    /**
     * Phonetic vowels mapping.
     */
    protected static array $vowels = [
        'aa' => ['ind' => 'ஆ', 'sign' => 'ா'],
        'ee' => ['ind' => 'ஈ', 'sign' => 'ீ'],
        'ea' => ['ind' => 'ஏ', 'sign' => 'ே'],
        'oo' => ['ind' => 'ஊ', 'sign' => 'ூ'],
        'ae' => ['ind' => 'ஏ', 'sign' => 'ே'],
        'ai' => ['ind' => 'ஐ', 'sign' => 'ை'],
        'au' => ['ind' => 'ஔ', 'sign' => 'ௌ'],
        'oa' => ['ind' => 'ஓ', 'sign' => 'ோ'],
        'oh' => ['ind' => 'ஓ', 'sign' => 'ோ'],
        'ou' => ['ind' => 'ஔ', 'sign' => 'ௌ'],
        'ow' => ['ind' => 'ஔ', 'sign' => 'ௌ'],
        'a' => ['ind' => 'அ', 'sign' => ''],
        'i' => ['ind' => 'இ', 'sign' => 'ி'],
        'u' => ['ind' => 'உ', 'sign' => 'ு'],
        'e' => ['ind' => 'எ', 'sign' => 'ெ'],
        'o' => ['ind' => 'ஒ', 'sign' => 'ொ'],
    ];

    /**
     * Detect the typing style of the given text.
     * Returns: 'tanglish', 'tamil', 'english', or 'mixed'.
     */
    public function detectStyle(string $text): string
    {
        $hasTamil = (bool) preg_match('/[\x{0B80}-\x{0BFF}]/u', $text);
        $hasEnglish = (bool) preg_match('/[a-zA-Z]/', $text);

        if ($hasTamil && $hasEnglish) {
            return 'mixed';
        }

        if ($hasTamil) {
            return 'tamil';
        }

        // Check if there are known Tanglish words
        $words = preg_split('/[^a-zA-Z0-9\']+/u', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $tanglishCount = 0;
        $englishCount = 0;

        foreach ($words as $word) {
            if (isset(self::$tanglishToTamil[$word])) {
                $tanglishCount++;
            } elseif ($this->isEnglishPreserved($word)) {
                $englishCount++;
            } else {
                // If it contains patterns common in Tamil transliteration but rare in English (e.g. ending in nga, eenga, ruku, etc.)
                if (preg_match('/(nga|eenga|ruku|la|dhaan|than|athu|ithu|unga|enakku)$/', $word)) {
                    $tanglishCount++;
                }
            }
        }

        if ($tanglishCount > 0 && ($tanglishCount >= $englishCount || $tanglishCount >= 2)) {
            return 'tanglish';
        }

        return 'english';
    }

    /**
     * Generate suggestions for script conversion.
     * Returns null if no suggestion is appropriate.
     */
    public function suggest(string $text): ?string
    {
        // 1. Detect language / script style
        $style = $this->detectStyle($text);

        // Pre-replace certain pattern phrases to make it flow better.
        // e.g. "article ah" -> "article-ஐ"
        $modifiedText = preg_replace('/\barticle\s+ah\b/i', 'article-ஐ', $text);
        $modifiedText = preg_replace('/\barticle-ah\b/i', 'article-ஐ', $modifiedText);
        $modifiedText = preg_replace('/\barticle\s+ai\b/i', 'article-ஐ', $modifiedText);
        $modifiedText = preg_replace('/\barticle-ai\b/i', 'article-ஐ', $modifiedText);

        // 2. Perform conversion based on detected script style
        if ($style === 'tamil') {
            // Check for Tamil-script English words (like "ஐ லவ் யூ")
            return $this->convertTamilScriptEnglish($modifiedText);
        }

        if ($style === 'tanglish' || $style === 'mixed') {
            // Convert Tanglish/Mixed to Tamil
            return $this->transliterateTanglish($modifiedText);
        }

        return null;
    }

    /**
     * Convert Tamil-script English words (like "ஐ லவ் யூ") to English script.
     */
    protected function convertTamilScriptEnglish(string $text): ?string
    {
        // Tokenize by word to preserve punctuation and spaces
        $tokens = preg_split('/(\s+|,|\.|\?|!|:|;|\(|\))/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $converted = false;
        $result = '';

        foreach ($tokens as $token) {
            $trimmed = trim($token);
            // If it's punctuation/spaces, append as is
            if ($trimmed === '' || preg_match('/^[\s+,\.\?!:;\(\)]+$/u', $trimmed)) {
                $result .= $token;
                continue;
            }

            // Look up in the Tamil to English dictionary
            if (isset(self::$tamilToEnglish[$trimmed])) {
                $result .= self::$tamilToEnglish[$trimmed];
                $converted = true;
            } else {
                $result .= $token;
            }
        }

        return $converted ? $result : null;
    }

    /**
     * Transliterate Tanglish words inside the text to Tamil script.
     */
    protected function transliterateTanglish(string $text): ?string
    {
        // Tokenize by word boundary to preserve spacing, punctuation, and HTML safety
        $tokens = preg_split('/(\s+|,|\.|\?|!|:|;|\(|\)|<[^>]*>)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $converted = false;
        $result = '';

        foreach ($tokens as $token) {
            // If it's punctuation, spaces, or an HTML tag, append as is
            if ($token === '' || preg_match('/^[\s+,\.\?!:;\(\)]+$/u', $token) || (str_starts_with($token, '<') && str_ends_with($token, '>'))) {
                $result .= $token;
                continue;
            }

            // If it already contains Tamil script, keep as is
            if (preg_match('/[\x{0B80}-\x{0BFF}]/u', $token)) {
                $result .= $token;
                continue;
            }

            // Attempt to transliterate the word
            $transliterated = $this->transliterateWord($token);
            if ($transliterated !== $token) {
                $result .= $transliterated;
                $converted = true;
            } else {
                $result .= $token;
            }
        }

        return $converted ? $result : null;
    }

    /**
     * Transliterate a single word.
     */
    protected function transliterateWord(string $word): string
    {
        $lowercaseWord = strtolower($word);

        // 1. Direct dictionary match
        if (isset(self::$tanglishToTamil[$lowercaseWord])) {
            return self::$tanglishToTamil[$lowercaseWord];
        }

        // 2. English word preservation (do not transliterate common English words)
        if ($this->isEnglishPreserved($lowercaseWord)) {
            return $word;
        }

        // If it's short/single letter and not a vowel, preserve it
        if (strlen($lowercaseWord) === 1 && !in_array($lowercaseWord, ['a', 'e', 'i', 'o', 'u'], true)) {
            return $word;
        }

        // 3. Phonetic transliteration loop
        $len = strlen($lowercaseWord);
        $i = 0;
        $output = '';

        while ($i < $len) {
            $matchedConsonant = null;
            $consonantLen = 0;

            // Try to match consonants at index $i (longest first)
            foreach (self::$consonants as $key => $mapping) {
                $kLen = strlen($key);
                if ($i + $kLen <= $len && substr($lowercaseWord, $i, $kLen) === $key) {
                    $matchedConsonant = $mapping;
                    $consonantLen = $kLen;
                    break;
                }
            }

            if ($matchedConsonant) {
                $i += $consonantLen;

                // Check if followed by a vowel
                $matchedVowel = null;
                $vowelLen = 0;

                foreach (self::$vowels as $key => $mapping) {
                    $kLen = strlen($key);
                    if ($i + $kLen <= $len && substr($lowercaseWord, $i, $kLen) === $key) {
                        $matchedVowel = $mapping;
                        $vowelLen = $kLen;
                        break;
                    }
                }

                if ($matchedVowel) {
                    $i += $vowelLen;
                    // Consonant + Vowel combination
                    // If it is the initial 'n', map it to 'ந' base
                    if ($consonantLen === 1 && $lowercaseWord[$i - $vowelLen - 1] === 'n' && ($i - $vowelLen - 1 === 0)) {
                        $base = 'ந';
                    } else {
                        $base = $matchedConsonant['base'];
                    }
                    $output .= $base . $matchedVowel['sign'];
                } else {
                    // Consonant at end of syllable/word, so it gets its pulli dot form
                    // Adjust if it is initial 'n' followed by nothing (which shouldn't happen, but just in case)
                    if ($consonantLen === 1 && $lowercaseWord[$i - 1] === 'n' && ($i - 1 === 0)) {
                        $output .= 'ந்';
                    } else {
                        $output .= $matchedConsonant['dot'];
                    }
                }
            } else {
                // Try to match vowel at index $i (longest first)
                $matchedVowel = null;
                $vowelLen = 0;

                foreach (self::$vowels as $key => $mapping) {
                    $kLen = strlen($key);
                    if ($i + $kLen <= $len && substr($lowercaseWord, $i, $kLen) === $key) {
                        $matchedVowel = $mapping;
                        $vowelLen = $kLen;
                        break;
                    }
                }

                if ($matchedVowel) {
                    $i += $vowelLen;
                    $output .= $matchedVowel['ind'];
                } else {
                    // No match, copy character (e.g. symbol, number)
                    $output .= $word[$i];
                    $i++;
                }
            }
        }

        return $output;
    }

    /**
     * Get up to 5 candidate transliterations for a single word.
     */
    public function getCandidates(string $word): array
    {
        $lowercaseWord = strtolower($word);
        
        // If it's a preserved English word (but not in our custom dictionary), don't transliterate it
        if ($this->isEnglishPreserved($lowercaseWord) && !isset(self::$customWordCandidates[$lowercaseWord])) {
            return [$word];
        }

        // Generate high-priority phonetic candidates based on common spoken vowel rules in Tanglish spelling
        $highPriorityCandidates = [];
        $variants = [];

        // 0. Colloquial past-tense third-person neuter singular endings (e.g. -thichu, -dhichu, -dichu, -nthichu)
        if (preg_match('/(th|dh|d|nth)(ichu|uchu|ichi)$/', $lowercaseWord, $matches)) {
            $base = preg_replace('/(th|dh|d|nth)(ichu|uchu|ichi)$/', '', $lowercaseWord);
            $c = $matches[1];
            $variants[] = $base . $c . 'ichchu';
            $variants[] = $base . $c . 'uchchu';
            $variants[] = $base . $c . 'ichchi';
        }

        // 1. Spoken suffix -en -> -aen (e.g. irukken -> irukkaen -> இருக்கேன்)
        if (str_ends_with($lowercaseWord, 'en') && !str_ends_with($lowercaseWord, 'aen')) {
            $variants[] = substr($lowercaseWord, 0, -2) . 'aen';
        }


        // 2. Vowel pattern ei -> aey (ேய்), ae (ே), ai (ை)
        if (str_contains($lowercaseWord, 'ei')) {
            $variants[] = str_replace('ei', 'aey', $lowercaseWord);
            $variants[] = str_replace('ei', 'ae', $lowercaseWord);
            $variants[] = str_replace('ei', 'ai', $lowercaseWord);
        }

        // 3. Vowel pattern ey -> aey (ேய்), ae (ே)
        if (str_contains($lowercaseWord, 'ey')) {
            $variants[] = str_replace('ey', 'aey', $lowercaseWord);
            $variants[] = str_replace('ey', 'ae', $lowercaseWord);
        }

        // 4. Vowel pattern ae -> aey
        if (str_contains($lowercaseWord, 'ae') && !str_contains($lowercaseWord, 'aey')) {
            $variants[] = str_replace('ae', 'aey', $lowercaseWord);
        }

        // 5. Spoken suffix -e -> -ae (e.g. inge -> ingae -> இங்கே)
        if (str_ends_with($lowercaseWord, 'e') && !preg_match('/[aeiou]e$/', $lowercaseWord)) {
            $variants[] = substr($lowercaseWord, 0, -1) . 'ae';
        }

        // 6. Vowel pattern ou -> avu (e.g. sound -> savund -> சவுண்ட்)
        if (str_contains($lowercaseWord, 'ou')) {
            $variants[] = str_replace('ou', 'avu', $lowercaseWord);
        }

        // 7. Support short 'e' representing long 'ae' (ே)
        if (str_contains($lowercaseWord, 'e')) {
            $replaced = preg_replace('/(?<![aeiou])e(?![aeiou])/', 'ae', $lowercaseWord);
            if ($replaced !== $lowercaseWord) {
                $variants[] = $replaced;
            }
        }

        // 8. Support single 'o' representing long 'oa' (ோ)
        if (str_contains($lowercaseWord, 'o')) {
            $replaced = preg_replace('/(?<![aeiou])o(?![aeiou])/', 'oa', $lowercaseWord);
            if ($replaced !== $lowercaseWord) {
                $variants[] = $replaced;
            }
        }

        $highPriorityVariants = array_unique($variants);

        // Generate lower-priority consonant substitutions (d vs th/dh)
        $lowPriorityVariants = [];

        // Consonant substitutions on the vowel/suffix variants
        foreach ($variants as $v) {
            if (str_contains($v, 'd') && !str_contains($v, 'dh')) {
                $lowPriorityVariants[] = str_replace('d', 'th', $v);
                $lowPriorityVariants[] = str_replace('d', 'dh', $v);
            }
            if (str_contains($v, 'th')) {
                $lowPriorityVariants[] = str_replace('th', 'd', $v);
            }
            if (str_contains($v, 'dh')) {
                $lowPriorityVariants[] = str_replace('dh', 'd', $v);
            }
        }

        // Consonant substitutions on the original word
        if (str_contains($lowercaseWord, 'd') && !str_contains($lowercaseWord, 'dh')) {
            $lowPriorityVariants[] = str_replace('d', 'th', $lowercaseWord);
            $lowPriorityVariants[] = str_replace('d', 'dh', $lowercaseWord);
        }
        if (str_contains($lowercaseWord, 'th')) {
            $lowPriorityVariants[] = str_replace('th', 'd', $lowercaseWord);
        }
        if (str_contains($lowercaseWord, 'dh')) {
            $lowPriorityVariants[] = str_replace('dh', 'd', $lowercaseWord);
        }

        $lowPriorityVariants = array_unique($lowPriorityVariants);

        // Transliterate high priority variants
        foreach ($highPriorityVariants as $variant) {
            if ($variant !== $lowercaseWord) {
                $trans = $this->transliterateWord($variant);
                if ($trans !== $variant && !in_array($trans, $highPriorityCandidates, true)) {
                    $highPriorityCandidates[] = $trans;
                }
            }
        }

        // Transliterate low priority variants
        $lowPriorityCandidates = [];
        foreach ($lowPriorityVariants as $variant) {
            if ($variant !== $lowercaseWord && !in_array($variant, $highPriorityVariants, true)) {
                $trans = $this->transliterateWord($variant);
                if ($trans !== $variant && !in_array($trans, $lowPriorityCandidates, true) && !in_array($trans, $highPriorityCandidates, true)) {
                    $lowPriorityCandidates[] = $trans;
                }
            }
        }

        $candidates = [];

        // 1. Check custom multi-candidate dictionary first
        if (isset(self::$customWordCandidates[$lowercaseWord])) {
            foreach (self::$customWordCandidates[$lowercaseWord] as $cand) {
                if (!in_array($cand, $candidates, true)) {
                    $candidates[] = $cand;
                }
            }
        }

        // 2. Direct single dictionary match fallback
        if (isset(self::$tanglishToTamil[$lowercaseWord])) {
            $dictMatch = self::$tanglishToTamil[$lowercaseWord];
            if (!in_array($dictMatch, $candidates, true)) {
                $candidates[] = $dictMatch;
            }
        }

        // 3. Add high priority variant transliterations
        foreach ($highPriorityCandidates as $cand) {
            if (!in_array($cand, $candidates, true)) {
                $candidates[] = $cand;
            }
        }

        // 4. Core phonetic translation
        $phoneticTamil = $this->transliterateWord($word);
        if ($phoneticTamil !== $word && !in_array($phoneticTamil, $candidates, true)) {
            $candidates[] = $phoneticTamil;
        }

        // 5. Generate smart phonetic variations of core phonetic translation
        $variations = $this->generatePhoneticVariations($phoneticTamil);
        foreach ($variations as $var) {
            if ($var !== $word && !in_array($var, $candidates, true)) {
                $candidates[] = $var;
            }
        }

        // 6. Generate variations for the high priority candidates to be thorough
        foreach ($highPriorityCandidates as $hpc) {
            $vars = $this->generatePhoneticVariations($hpc);
            foreach ($vars as $v) {
                if ($v !== $word && !in_array($v, $candidates, true)) {
                    $candidates[] = $v;
                }
            }
        }

        // 7. Add low priority variant transliterations
        foreach ($lowPriorityCandidates as $cand) {
            if (!in_array($cand, $candidates, true)) {
                $candidates[] = $cand;
            }
        }

        // 8. Generate variations for the low priority candidates
        foreach ($lowPriorityCandidates as $lpc) {
            $vars = $this->generatePhoneticVariations($lpc);
            foreach ($vars as $v) {
                if ($v !== $word && !in_array($v, $candidates, true)) {
                    $candidates[] = $v;
                }
            }
        }

        // Limit Tamil candidates to 5
        $candidates = array_slice($candidates, 0, 5);

        // 9. Add the original word as the last option if not already in the list
        if (!in_array($word, $candidates, true)) {
            $candidates[] = $word;
        }

        return $candidates;
    }


    /**
     * Generate common spelling/phonetic variations in Tamil script.
     */
    protected function generatePhoneticVariations(string $tamilWord): array
    {
        $variations = [];
        
        $replacements = [
            'ன' => ['ண'],
            'ண' => ['ன'],
            'ல' => ['ள', 'ழ'],
            'ள' => ['ல', 'ழ'],
            'ழ' => ['ள', 'ல'],
            'ர' => ['ற'],
            'ற' => ['ர'],
        ];
        
        foreach ($replacements as $from => $toList) {
            if (mb_strpos($tamilWord, $from) !== false) {
                foreach ($toList as $to) {
                    $variations[] = str_replace($from, $to, $tamilWord);
                }
            }
        }
        
        // Smart starting character replacements (e.g. 'எ' -> 'ஏ' or 'யெ')
        if (mb_strpos($tamilWord, 'எ') === 0) {
            $rest = mb_substr($tamilWord, 1);
            $variations[] = 'ஏ' . $rest;
            $variations[] = 'யெ' . $rest;
        } elseif (mb_strpos($tamilWord, 'ஏ') === 0) {
            $rest = mb_substr($tamilWord, 1);
            $variations[] = 'எ' . $rest;
        }

        // Grantha/Sanskrit variations for 'ச' -> 'ஸ' by replacing individual/combinatorial occurrences of 'ச'
        $saPositions = [];
        $len = mb_strlen($tamilWord);
        for ($idx = 0; $idx < $len; $idx++) {
            if (mb_substr($tamilWord, $idx, 1) === 'ச') {
                $saPositions[] = $idx;
            }
        }

        $numPositions = count($saPositions);
        if ($numPositions > 0 && $numPositions <= 4) { // safety limit to prevent exponential explosion
            $numCombinations = 1 << $numPositions;
            for ($c = 1; $c < $numCombinations; $c++) {
                $chars = [];
                for ($idx = 0; $idx < $len; $idx++) {
                    $chars[] = mb_substr($tamilWord, $idx, 1);
                }
                for ($p = 0; $p < $numPositions; $p++) {
                    if (($c & (1 << $p)) !== 0) {
                        $chars[$saPositions[$p]] = 'ஸ';
                    }
                }
                $variations[] = implode('', $chars);
            }
        }

        // Add standard static single replaces for other formats as fallback
        $sanskritReplacements = [
            'ச்' => 'ஸ்',
            'சு' => 'ஸு',
            'செ' => 'ஸெ',
            'சே' => 'ஸே',
            'சொ' => 'ஸொ',
            'சோ' => 'ஸோ',
            'சை' => 'ஸை',
        ];

        foreach ($sanskritReplacements as $from => $to) {
            if (mb_strpos($tamilWord, $from) !== false) {
                $variations[] = str_replace($from, $to, $tamilWord);
            }
        }

        return $variations;
    }
}

