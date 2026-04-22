<?php
return [
    "disclosure" => [
        "label" => "Obertura i documentació (Disclosure)",
        "weight" => 20,
        "help" => "Fa referència al grau en què existeixen especificacions completes del format i eines per validar-ne la integritat tècnica, i fins a quin punt aquesta informació és accessible per a les persones i institucions que creen, gestionen o preserven contingut digital.",
        "examples" => [
            0 => "Un format totalment tancat, sense especificació pública i sense eines conegudes de validació.",
            1 => "Un format propietari amb informació molt parcial o difícil d'obtenir, depenent sobretot del fabricant.",
            2 => "Un format amb documentació disponible però incompleta, dispersa o amb llacunes rellevants.",
            3 => "Un format amb especificació pública raonablement completa i amb algunes eines de validació o inspecció.",
            4 => "Un format amb especificació oberta, completa, revisable externament i amb eines de validació accessibles."
        ],
        "options" => [
            0 => "Molt baix — Les especificacions no són disponibles o són molt restringides.",
            1 => "Baix — Documentació parcial o incompleta.",
            2 => "Mitjà — Especificacions accessibles però amb restriccions o llacunes.",
            3 => "Alt — Especificacions obertes i mantingudes.",
            4 => "Molt alt — Totalment obert i sense cap restricció significativa."
        ]
    ],
    "adoption" => [
        "label" => "Ús i adopció (Adoption)",
        "weight" => 20,
        "help" => "Mesura fins a quin punt el format és utilitzat pels principals creadors, distribuïdors, aplicacions i usuaris d'informació. Un format àmpliament adoptat acostuma a tenir més eines, més suport del mercat i menys risc d'obsolescència ràpida.",
        "examples" => [
            0 => "Un format gairebé desconegut, limitat a un producte o entorn molt específic.",
            1 => "Un format utilitzat només en un nínxol reduït o dins d'un ecosistema concret.",
            2 => "Un format amb una adopció moderada, present en alguns fluxos de treball però no generalitzat.",
            3 => "Un format àmpliament utilitzat en un sector o en diversos entorns de producció i accés.",
            4 => "Un format clarament consolidat, amb suport extens i considerat estàndard de facto."
        ],
        "options" => [
            0 => "Molt baix — Ús extremadament limitat.",
            1 => "Baix — Ús restringit a nínxols molt concrets.",
            2 => "Mitjà — Adopció moderada.",
            3 => "Alt — Amplament utilitzat.",
            4 => "Molt alt — Estàndard de facto o molt consolidat."
        ]
    ],
    "transparency" => [
        "label" => "Transparència del format (Transparency)",
        "weight" => 10,
        "help" => "Indica fins a quin punt la representació digital és analitzable directament amb eines bàsiques, fins i tot amb un editor de text quan escau. Com més simple i llegible és la representació, més fàcil serà migrar-la, reinterpretar-la o fer-ne arqueologia digital.",
        "examples" => [
            0 => "Un format xifrat o altament opac, impossible d'interpretar sense programari específic.",
            1 => "Un format binari complex, amb estructura poc observable i forta dependència d'eines especialitzades.",
            2 => "Un format parcialment interpretable, però amb parts crítiques comprimides, encapsulades o difícils d'entendre.",
            3 => "Un format basat en estructures conegudes, amb parts inspeccionables i certa facilitat d'anàlisi.",
            4 => "Un format molt transparent, llegible o analitzable directament amb eines bàsiques."
        ],
        "options" => [
            0 => "Molt baix — Il·legible sense programari específic o opac per definició.",
            1 => "Baix — Accés parcial i estructura complexa.",
            2 => "Mitjà — Llegible parcialment o amb transparència limitada.",
            3 => "Alt — Basat en estructures conegudes o obertes, però no totalment transparent.",
            4 => "Molt alt — Totalment transparent."
        ]
    ],
    "selfdoc" => [
        "label" => "Autodescripció (Self-documentation)",
        "weight" => 10,
        "help" => "Avalua si el fitxer pot incorporar metadades descriptives, tècniques, administratives i de context que ajudin a entendre'l, gestionar-lo i preservar-lo al llarg del temps. També és rellevant el suport a elements d'accessibilitat digital quan el format ho permet.",
        "examples" => [
            0 => "Un format que no permet incorporar metadades internes útils ni informació contextual rellevant.",
            1 => "Un format amb metadades molt mínimes o poc estructurades.",
            2 => "Un format amb capacitats bàsiques de metadades, però limitades per a preservació.",
            3 => "Un format que pot integrar metadades descriptives i tècniques de manera estructurada.",
            4 => "Un format molt ric en metadades internes, context, traçabilitat i suport d'accessibilitat quan escau."
        ],
        "options" => [
            0 => "Molt baix — Sense metadades internes útils.",
            1 => "Baix — Metadades mínimes.",
            2 => "Mitjà — Metadades bàsiques.",
            3 => "Alt — Suport estructurat de metadades.",
            4 => "Molt alt — Totalment autodescriptiu."
        ]
    ],
    "dependencies" => [
        "label" => "Dependències externes (External dependencies)",
        "weight" => 15,
        "help" => "Fa referència al grau en què el format depèn d'un maquinari, sistema operatiu o programari concrets per ser utilitzat o representat correctament. Com més dependències crítiques té, més costosa i fràgil pot ser la seva preservació.",
        "examples" => [
            0 => "Un format inutilitzable fora d'un dispositiu, sistema o programari concret i tancat.",
            1 => "Un format fortament lligat a una aplicació o plataforma determinada, amb poques alternatives reals.",
            2 => "Un format amb dependències importants però substituïbles amb esforç.",
            3 => "Un format que es pot obrir o convertir amb diverses eines i entorns.",
            4 => "Un format pràcticament independent, interoperable i amb múltiples opcions de lectura o tractament."
        ],
        "options" => [
            0 => "Risc molt alt — Requereix programari o maquinari propietari.",
            1 => "Alt — Dependències no garantides a llarg termini.",
            2 => "Mitjà — Dependències comunes i substituïbles.",
            3 => "Baix — Compatible amb diverses eines, incloses eines obertes.",
            4 => "Molt baix — Completament independent o altament interoperable."
        ]
    ],
    "patents" => [
        "label" => "Impacte de patents (Patents)",
        "weight" => 10,
        "help" => "Considera si les patents o les condicions de llicència poden dificultar la preservació, la creació d'eines obertes, la transcodificació o l'accés sostingut al contingut. El problema no és l'existència de patents en si mateixa, sinó les restriccions que puguin implicar.",
        "examples" => [
            0 => "Un format amb patents actives i restriccions severes que poden dificultar-ne la preservació.",
            1 => "Un format amb llicències o patents potencialment problemàtiques i costos difícils de preveure.",
            2 => "Un format amb una situació mixta: patents existents però amb llicències relativament assumibles o expirades parcialment.",
            3 => "Un format sense impactes pràctics coneguts de patents per a la preservació.",
            4 => "Un format clarament lliure de restriccions rellevants de patents."
        ],
        "options" => [
            0 => "Risc molt alt — Format patentat amb fortes restriccions.",
            1 => "Alt — Patents o llicències parcialment restrictives.",
            2 => "Mitjà — Patents expirades, limitades o sota esquemes assumibles.",
            3 => "Baix — Sense restriccions conegudes rellevants.",
            4 => "Molt baix — Totalment lliure de patents problemàtiques."
        ]
    ],
    "tpm" => [
        "label" => "Mecanismes de protecció tècnica",
        "weight" => 15,
        "help" => "Avalua fins a quin punt l'ús del format pot quedar limitat per mecanismes tècnics de protecció, com ara xifrat, vinculació a dispositius, control d'accés o connexió obligatòria. Aquests mecanismes poden impedir còpies de preservació, migració i accés futur.",
        "examples" => [
            0 => "Un ús del format en què el contingut queda bloquejat per DRM o xifrat sense control del repositori.",
            1 => "Un format en què els mecanismes de protecció tècnica (incloent DRM, xifrat, control d'accés, lligam a dispositius o altres restriccions tècniques d'ús) és molt freqüent i condiciona fortament l'accés o la còpia.",
            2 => "Un format que pot incorporar mecanismes de protecció tècnica (incloent DRM, xifrat, control d'accés, lligam a dispositius o altres restriccions tècniques d'ús) en alguns contextos, però no de manera necessària.",
            3 => "Un format que habitualment circula sense mecanismes de protecció tècnica o amb restriccions poc habituals.",
            4 => "Un format que no està pensat per incorporar mecanismes de protecció tècnica (incloent DRM, xifrat, control d'accés, lligam a dispositius o altres restriccions tècniques d'ús) o que, en la pràctica, no en depèn."
        ],
        "options" => [
            0 => "Risc molt alt — mecanismes de protecció tècnica obligatoris o fortament limitants.",
            1 => "Alt — mecanismes de protecció tècnica habitual o molt probable.",
            2 => "Mitjà — mecanismes de protecció tècnica possibles, però no inherents ni constants.",
            3 => "Baix — Sense mecanismes de protecció tècnica per defecte.",
            4 => "Molt baix — No acostuma a incorporar mecanismes de protecció tècnica o no n'hi ha dependència."
        ]
    ]
];
