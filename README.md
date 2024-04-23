[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.11047474.svg)](https://doi.org/10.5281/zenodo.11047474)
# E-RIHS Controlled List Links

[E-RIHS (European Research Infrastructure for Heritage Science)](https://www.e-rihs.eu/), is an initiative that supports the research, conservation, and management of cultural heritage through advanced scientific resources and methodologies, fostering collaboration among European institutions and enhancing knowledge and preservation of heritage assets. **E-RIHS** will provide access to distributed research facilities, archives and data to advance heritage science. The [E-RIHS Knowledgebase](https://data.e-rihs.io), based on the [Cordra system](https://www.cordra.org/), capturing the key metadata describing details of the services offered under **E-RIHS** and the research work carried out through and related to these services is being developed. A key component of this work is the use of shared terms and descriptions to describe tag and group the stored information. All of these shared terms are being gathered together within the [E-RIHS Vocabulary Server](http://vocab.e-rihs.io), based on the [OpenTheso System](https://opentheso.hypotheses.org/).

The software presented within this repository has been put together to simplify and reformat the data that can be pulled from the [E-RIHS Vocabulary Server API](https://vocab.e-rihs.io/openapi/doc/) so that it can be easily integrated into the various schema documents used to defined all of the entities modeled with the **E-RIHS Knowledgebase**, details of these schema can also be found within the [E-RIHS Schema GitHub repository](https://github.com/E-RIHS/schema)

## Using the system

The current live version of this script can be found at: https://hdl.handle.net/21.11158/0003-a375-bce0-4571

The landing page lists all of the current **groups** defined within the **E-RIHS Vocabulary Server** and provides a series of preformatted links to access either cached or live versions of re-formatted data. Each of the groups can be viewed as **default**, **simple** and **full** formats.

The **E-RIHS Vocabulary Server** makes use of [Handle PIDs](https://en.wikipedia.org/wiki/Handle_System), if the code is pointed at a different instance of **OpenTheso System** the native IDs will be used and displayed.

> [!TIP]
> In order to speed up the use of the reformatted data it is possible to **cache** it in a local folder called **local**. If this folder is not generated with the correct permissions the script will default to providing refreshed live data each time it is used.

### The _default_ format
> [!TIP]
> See live data at: https://hdl.handle.net/21.11158/0002-8d19-3829-ba66
```json
{
    "id": "g8",
    "label": "offered_to",
    "created": "2024-04-22 16:12:13",
    "handle": "https://hdl.handle.net/21.11158/0002-8d19-3829-ba66",
    "list": {
        "https://hdl.handle.net/21.11158/0001-nx5l1g24rqw413kpxbcmh8jpq": "eu",
        "https://hdl.handle.net/21.11158/0001-x9p9pfsp7bqw0ftmn261fnlj1": "global",
        "https://hdl.handle.net/21.11158/0001-c2jr97fx4r0mbfswt87dk57mm": "institutional",
        "https://hdl.handle.net/21.11158/0001-1s89h4108kv7jcr33b7v0pjsd": "national"
    }
}
```

### The _simple_ format
> [!TIP]
> See live data at: https://hdl.handle.net/21.11158/0002-8d19-3829-ba66?urlappend=%26simple
```json
[
    "eu",
    "global",
    "institutional",
    "national"
]
```

### The _full_ format
> [!TIP]
> See live data at: https://hdl.handle.net/21.11158/0002-8d19-3829-ba66?urlappend=%26full
```json
{
    "id": "g8",
    "label": "offered_to",
    "created": "2024-04-22 16:16:27",
    "handle": "https://hdl.handle.net/21.11158/0002-8d19-3829-ba66?urlappend=%26full",
    "data": {
        "https://hdl.handle.net/21.11158/0001-nx5l1g24rqw413kpxbcmh8jpq": {
            "prefLabel": "eu",
            "definition": null,
            "altLabel": [],
            "narrower": [],
            "broader": {
                "https://hdl.handle.net/21.11158/0001-jl8k2l19dbmspq5fdpb0qcdfc": "offered to"
            },
            "term_json_url": "https://vocab.e-rihs.io/openapi/v1/concept/handle/21.11158/0001-nx5l1g24rqw413kpxbcmh8jpq"
        },
        "https://hdl.handle.net/21.11158/0001-x9p9pfsp7bqw0ftmn261fnlj1": {
            "prefLabel": "global",
            "definition": null,
            "altLabel": [],
            "narrower": [],
            "broader": {
                "https://hdl.handle.net/21.11158/0001-jl8k2l19dbmspq5fdpb0qcdfc": "offered to"
            },
            "term_json_url": "https://vocab.e-rihs.io/openapi/v1/concept/handle/21.11158/0001-x9p9pfsp7bqw0ftmn261fnlj1"
        },
        "https://hdl.handle.net/21.11158/0001-c2jr97fx4r0mbfswt87dk57mm": {
            "prefLabel": "institutional",
            "definition": null,
            "altLabel": [],
            "narrower": [],
            "broader": {
                "https://hdl.handle.net/21.11158/0001-jl8k2l19dbmspq5fdpb0qcdfc": "offered to"
            },
            "term_json_url": "https://vocab.e-rihs.io/openapi/v1/concept/handle/21.11158/0001-c2jr97fx4r0mbfswt87dk57mm"
        },
        "https://hdl.handle.net/21.11158/0001-1s89h4108kv7jcr33b7v0pjsd": {
            "prefLabel": "national",
            "definition": null,
            "altLabel": [],
            "narrower": [],
            "broader": {
                "https://hdl.handle.net/21.11158/0001-jl8k2l19dbmspq5fdpb0qcdfc": "offered to"
            },
            "term_json_url": "https://vocab.e-rihs.io/openapi/v1/concept/handle/21.11158/0001-1s89h4108kv7jcr33b7v0pjsd"
        }
    }
}
```

## Acknowledgement
This project was developed and tested as part of the work of the following projects:

### The H2020 [IPERION-HS](https://www.iperionhs.eu/) project
[<img height="64px" src="https://github.com/jpadfield/simple-modelling/blob/master/docs/graphics/IPERION-HS%20Logo.png" alt="IPERION-HS">](https://www.iperionhs.eu/)
###### [IPERION-HS has received funding from the European Union’s Horizon 2020 call H2020-INFRAIA-2019-1, under GA 871034](https://cordis.europa.eu/project/id/871034)
### The Horizon Europe [E-RIHS IP](https://www.e-rihs.eu/the-project/) project
[<img height="64px" src="https://e-rihs.io/graphics/e-rihs-eric-logo_ai.png" alt="E-RIHS IP Logo">](https://www.iperionhs.eu/)<br/>
###### [E-RIHS IP has received funding from the European Union’s Horizon Europe call HORIZON-INFRA-2021-DEV-02, Grant Agreement n.101079148.](https://cordis.europa.eu/project/id/101079148)
