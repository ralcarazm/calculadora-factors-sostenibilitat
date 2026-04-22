<?php
return [
    "epub" => [
        "label" => "Fitxa orientativa: EPUB 3.3",
        "format" => "EPUB",
        "version" => "3.3",
        "summary" => "Exemple orientatiu per a ús docent. Mostra una avaluació raonada d'EPUB com a format de publicació digital estructurada. No és una qualificació oficial ni universal: cal adaptar-la al cas concret, al tipus de document i a la presència o no de mecanismes de protecció tècnica.",
        "student" => "",
        "values" => [
            "disclosure" => 4,
            "adoption" => 3,
            "transparency" => 3,
            "selfdoc" => 4,
            "dependencies" => 3,
            "patents" => 3,
            "tpm" => 2
        ],
        "notes" => [
            "disclosure" => "La documentació de la família EPUB és pública i molt accessible.",
            "adoption" => "És un format àmpliament utilitzat en publicació digital, encara que no és universal en tots els entorns.",
            "transparency" => "Internament combina recursos coneguts com XHTML, CSS i XML dins d'un contenidor comprimit, cosa que el fa relativament transparent però no tan directe com un fitxer de text pla.",
            "selfdoc" => "Pot incorporar metadades descriptives i estructurals de manera rica.",
            "dependencies" => "La lectura depèn d'un sistema o aplicació compatible i algunes funcionalitats poden variar segons el lector.",
            "patents" => "Com a estàndard base, EPUB no sol presentar restriccions de patents especialment problemàtiques; els possibles condicionants acostumen a venir de tecnologies incrustades o associades, com alguns còdecs multimèdia.",
            "tpm" => "Pot distribuir-se amb mecanismes de protecció tècnica en entorns comercials, però aquests mecanismes no són intrínsecs al model base del format EPUB."
        ],
        "justifications" => [
            "disclosure" => "",
            "adoption" => "",
            "transparency" => "",
            "selfdoc" => "",
            "dependencies" => "",
            "patents" => "",
            "tpm" => ""
        ],
        "evidence" => [
            "disclosure" => "W3C EPUB 3.3; documentació pública del format. https://www.w3.org/TR/epub-33/",
            "adoption" => "Observació del mercat editorial digital i compatibilitat de molts lectors. https://idpf.org/news/ibm_adopts_epub-supporter-quotes",
            "transparency" => "Estructura basada en HTML/XHTML, CSS, XML i contenidor ZIP.",
            "selfdoc" => "Paquet EPUB amb metadades, navegació i recursos estructurats.",
            "dependencies" => "Necessita sistemes de lectura compatibles i el comportament pot variar segons el lector.",
            "patents" => "W3C EPUB 3.3 i IPR del grup EPUB 3: el format base no presenta declaracions de patents, però alguns recursos incrustats, com certs còdecs de vídeo o àudio, poden tenir implicacions de royalties o llicència. https://www.w3.org/TR/epub-33/ https://www.w3.org/groups/wg/epub/ipr/",
            "tpm" => "Presència de DRM en alguns canals comercials de distribució."
        ]
    ]
];
