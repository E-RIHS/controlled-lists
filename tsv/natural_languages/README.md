# Natural Languages

This list is intended to allow users to select standard defined natural languages. 

### Referenced by
* Field: **service_operating_languages** in the [Service metadata schema](https://github.com/E-RIHS/schema#service-metadata-schema)

> **Note**
> 
> This controlled list has been prepared in relation to the development of the standard metadata modelling and data gathering work being carried out within [IPERION-HS](https://cordis.europa.eu/project/id/871034) and [E-RIHS IP](https://cordis.europa.eu/project/id/101079148) as part of the development of [E-RIHS](https://www.e-rihs.eu/). It is anticipated that lists presented here will be migrated to a full E-RIHS vocabulary system, once it has been made available.
>
> ### List Format
> 
> The terms included within this list should be saved within Tab Separated Value (TSV) files within this folder with the following structure:
> 
> |DB Term |Display Name | Term Description | Same As | Source |
> | ------------- |:-------------:| -----:| -----:| -----:|
> | term_id_1 | Display Name 1 | Description 1 | Same As URL 1 | - |
> | term_id_2 | Display Name 2 | Description 2 | - | Source URL 2 |
> 
> * **term_id**: name_formatted_as_an_id 
> * **Display Name**: _(language specific)_ Indicate how the term should appear when actually displayed in a drop-down list or related form.
> **Description**: _(language specific)_ A short free text description of what the term means - this field can be left blank for terms that have a direct Same As URL that already contains a full description of the term. (An existing description can be copied in, but please cite the source)
> * **Same As URL**: _(optional where appropriate)_ Where a term is directly taken or can be directly referenced to and exiting standard term, for example from [AAT](https://www.getty.edu/research/tools/vocabularies/aat/) or [Wikidata](https://www.wikidata.org/) include the URL for that term. 
> * **Source URL**: _(optional where appropriate)_ If a term is not a direct version of an existing term, but is related to or is a derivative of an existing term/process/method include an appropriate URL.
>
> ### List Language
>
> The default language of these controlled lists is expected to be English but alternative languages files can also be save here. the TSV files should be named as shown here:
> * controlled_list_tag_LanguageCode.tsv
> * For example: **service_readiness_level_en.tsv**
