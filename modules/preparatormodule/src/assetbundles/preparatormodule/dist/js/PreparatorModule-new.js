/**
 * preparator module for Craft CMS
 *
 * preparator JS
 *
 * @author    @lostika
 * @copyright Copyright (c) 2019 @lostika
 * @link      cstudios.sk
 * @package   PreparatorModule
 * @since     1.0.0
 */
let appModule2 = $('#app.vue-konfigurator');
if (appModule2.length > 0) {

    let app = new Vue({
        el: '#app.vue-konfigurator',
        delimiters: ['{','}'],
        data: {
            activeHouseVersionNumber : 0,
            completisation : 'A',
            completisationPrice : 0,
            selectorsIndexer : [],
            houseTypeSelector: [],

            houseTypeSelectorModels: [],
            energyCombination : {},
            versionPrice : {
                a : 0,
                b : 0,
                c : 0,
                d : 0,
            },
            requestData : {},
            houseId : 0,
            ea : '',
            ca : '',
            rek : 0,
            primaryEnergy : 0,
            energyClass : 'G',
            totalYearlyEnergy : 0,
            totalMonthlyEnergyPrice : 0,
            demo : {},
            possibilities: [],
            calcFlow : [],
            props : {
                typeTitles : {
                    1:"Zakladanie",
                    2:"Stenový nosný systém",
                    3:"Zateplovací systém stien",
                    4:"Odovzdávací vykurovací systém",
                    5:"Zdroj tepla",
                    6:"Vetranie",
                    7:"Zateplenie stropu",
                    8:"Strešná krytina",
                    9:"Výplne otvorov",
                    10:"Štýl budovy"
                },
                typeOptions : {
                    1:{"col1":"Zakladanie na základových pásoch","col2":"Zakladanie na penovom skle","col3":"","col4":"","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    2:{"col1":"Montovaný stenový systém hr.120mm","col2":"Murovaný-nebrúsený hr.250mm","col3":"Murovaný-nebrúsený hr.300mm","col4":"Murovaný-nebrúsený hr.380mm","col5":"Murovaný-brúsený hr.250mm","col6":"Murovaný-brúsený hr.300mm","col7":"Murovaný-brúsený hr.380mm","col8":"Murovaný-brúsený hr.440mm","col9":"Murovaný-brúsený hr.500mm","col10":"Murovaný-brúsený s TI hr.250mm","col11":"Murovaný-brúsený s TI hr.300mm","col12":"Murovaný-brúsený s TI hr.380mm","col13":"Murovaný-brúsený s TI hr.440mm","col14":"Murovaný-brúsený s TI hr.500mm","col15":"Murovaný-pórobetónový hr.250mm","col16":"Murovaný-pórobetónový hr.300mm","col17":"Murovaný-pórobetónový hr.375mm","col18":"Murovaný-pórobetónový hr.450mm","col19":"Murovaný-pórobetónový hr.500mm","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    3:{"col1":"Z polystyrénu hr.150mm","col2":"z Polystyrénu hr.200mm","col3":"z Polystyrénu hr.250mm","col4":"z Polystyrénu hr.300mm","col5":"z Minerálnej vlny hr.150mm","col6":"z Minerálnej vlny hr.200mm","col7":"z Minerálnej vlny hr.250mm","col8":"z Minerálnej vlny hr.300mm","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    4:{"col1":"Teplovodné radiátorové vykurovanie","col2":"Teplovodné podlahové vykurovanie","col3":"Teplovodné stropné vykurovanie a chladenie","col4":"Elektrické podlahové vykurovanie","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    5:{"col1":"Plynový kondenzačný kotoľ","col2":"Splyňovací kotoľ na tuhé palivo","col3":"Tepelné čerpadlo","col4":"Elektrické vykurovanie","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    6:{"col1":"Prirodzené vetranie","col2":"S rekuperačným vetracím systémom","col3":"","col4":"","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    7:{"col1":"Z kamennej vlny hr.240mm","col2":"Z kamennej vlny hr.350mm","col3":"Fúkaná TI z PUR peny hr.240mm","col4":"Fúkaná TI z PUR peny hr.350mm","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    8:{"col1":"Terran Danubia Colorsystem","col2":"Bramac Renova / Moravská","col3":"Tondach","col4":"San Marco","col5":"","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    9:{"col1":"Plastové biele okná","col2":"Plastové farebné okná","col3":"Hliníkové okná","col4":"drevené okná","col5":"Drevené okná Exclusive","col6":"","col7":"","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""},
                    10:{"col1":"Base","col2":"Minimal","col3":"Scandinav","col4":"Loft","col5":"Romantic","col6":"Provence","col7":"Neoclassic","col8":"","col9":"","col10":"","col11":"","col12":"","col13":"","col14":"","col15":"","col16":"","col17":"","col18":"","col19":"","col20":"","col21":"","col22":"","col23":"","col24":"","col25":""}
                },
                versionTitles : {
                    1 : 'Montovaný',
                    2 : 'Murovaný',
                    3 : 'Moderný',
                    4 : 'Pasívny',
                    5 : 'Exkluzívny',
                    6 : 'Luxusný',
                },
                generated : {}


            }
        },
        created(){
            let urlParams = new URLSearchParams(window.location.search);
            this.houseId = parseInt( window.currentHouseId);

            this.requestData = {
                activeHouseVersionNumber: this.getActiveVersionNumber(),
                selectorsIndexer: this.getVersionSelectorIndexer(),
                houseId : this.getHouseId(),
            };

            this.eaSelected();
            this.caSelected();
        },
        mounted () {
            this.calculation();

        },
        methods: {
            resetEnergiesToDefault() {
                this.ea = '';
                this.ca = '';
            },
            completisationChange(version) {

                this.completisationPrice = this.versionPrice[version];
                
            },
            eaSelected() {

                switch (this.ea) {
                    case 'EA1':
                    case 'EA2':
                        this.ca = 'CA1';
                        break;
                    case 'EA3':
                        this.ca = 'CA2';
                        break;
                    case 'EA4':
                        this.ca = 'CA4';
                        break;
                    // default:
                    //     this.ca = 'CA1';
                }
                this.requestDataAssigner({
                    ea: this.ea,
                    ca: this.ca
                });
                // this.calculation();
            },
            caSelected() {
                this.requestDataAssigner({
                    ca: this.ca
                });


                // this.calculation();

            },
            getHouseId() {
                return this.houseId;
            },
            getActiveVersionNumber() {
                return this.activeHouseVersionNumber;
            },
            getVersionSelectorIndexer() {
                return this.selectorsIndexer;
            },
            onHouseTypeSelectChanged(typeKey,option,index,$event) {
                console.log('type key', typeKey);
                if (typeKey == 5){
                    // get the combination
                    let combination = this.energySourceAndOutputCombination();

                    // check the index of the model, to bind value
                    let outputIndex = this.selectorsIndexer.indexOf(4);
                    this.houseTypeSelectorModels[outputIndex] = combination[option.value][0];

                }
                

                this.heatingDefinition();

                this.requestDataAssigner(this.calculationData());
                this.eaSelected();
                this.caSelected();
                this.calculation();

            },
            heatingDefinition() {
                let source = {
                    1 : 'EA1',
                    2 : 'EA2',
                    3 : 'EA3',
                    4 : 'EA4',
                };

                let output = {
                    1 : 'CA1',
                    2 : 'CA2',
                    3 : 'CA3',
                    4 : 'CA4',
                };



                let sourceIndex = this.selectorsIndexer.indexOf(5);
                let outputIndex = this.selectorsIndexer.indexOf(4);

                let sourceValue = this.houseTypeSelectorModels[sourceIndex];
                let outputValue = this.houseTypeSelectorModels[outputIndex];

                this.ea = source[sourceValue];
                this.ca = output[outputValue];

            },
            energySourceAndOutputCombination() {
                return {
                    1 : [1,2],
                    2 : [1,2],
                    3 : [2,3],
                    4 : [4],
                };
            },
            isDisabled(typeKey, index, option) {
                let combination = this.energySourceAndOutputCombination();
                let sourceIndex = this.selectorsIndexer.indexOf(5);
                let sourceValue = this.houseTypeSelectorModels[sourceIndex];

                if (typeKey == 4) {
                    let valueOptions = combination[sourceValue];

                    // this.houseTypeSelectorModels[sourceIndex] = valueOptions[0];
                    let toDisable = !valueOptions.includes(option.value);

                    return toDisable;
                }

                return false;
            },
            withoutOptions(options) {
                return options.length < 1;
            },
            onHouseTypeNumberSelected(e) {
                this.resetEnergiesToDefault();
                this.activeHouseVersionNumber = parseInt(e.currentTarget.dataset.active_house_version_number);
                this.requestData.activeHouseVersionNumber = this.getActiveVersionNumber();
                this.requestData.selectorsIndexer = [];
                // this.requestData.selectorsIndexer = this.getVersionSelectorIndexer();
                delete this.requestData.selectorsOnTypeNumber;
                // console.log('e.currentTarget.dataset.active_house_version_number',e.currentTarget.dataset);
                this.calculation();
            },
            request() {

                return axios({
                    // url : 'https://familyhouse.sk/admin/preparator/reader/calculate',
                    url: typeof window.currentSiteBaseUrl === 'undefined' ? 'https://familyhouse.sk/2020' : window.currentSiteBaseUrl + '/admin/preparator/reader/calculate',
                    method: 'POST',
                    data: this.requestData,
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Access-Control-Allow-Origin' : 'https://familyhouse.sk'},
                });
            },
            requestDataAssigner(source) {
                return Object.assign(
                    this.requestData,
                    source
                )
            },
            calculation() {

                this.request().then(response => {

                    let data = response.data;
                    let houseVersions = data.houseVersions;
                    let houseTypes = data.houseTypes;
                    let selectorsIndexer = data.selectorsIndexer;
                    let selectorTypes = data.selectorsOnTypeNumber;
                    this.activeHouseVersionNumber = data.activeHouseTypeNumber;
                    this.versionPrice.a = houseVersions.A;
                    this.versionPrice.b = houseVersions.B;
                    this.versionPrice.c = houseVersions.C;
                    this.versionPrice.d = houseVersions.D;

                    let energy = data.energy;
                    this.energyClass = energy.energyClass;
                    this.primaryEnergy = energy.primaryEnergy;
                    this.totalYearlyEnergy = energy.totalYearlyEnergy;
                    this.totalMonthlyEnergyPrice = energy.totalMonthlyEnergyPrice;


                    this.houseTypeSelectorModels = selectorTypes;

                    this.houseTypeSelector = houseTypes;
                    this.selectorsIndexer = selectorsIndexer;
                    this.houseTypeSelectorModels = selectorTypes;
                    this.requestDataAssigner({selectorsIndexer: this.selectorsIndexer});
                    this.requestDataAssigner({selectorsOnTypeNumber: this.houseTypeSelectorModels});

                    this.demo = data.demo;
                    this.calcFlow = this.demo.calc.calcFlow;
                    this.possibilities = data.customs.possibilities;
                    this.props.generated = data.customs.calculator_properties;

                    // this.makeActiveStatusOnElements();
                    this.completisationChange(this.completisation.toLowerCase());

                    appModule2.css('height', 'auto');
                })
                    .catch(function (error) {
                        console.log('x' + error);
                    });


            },
            activeOption(index,typeKey,option) {
                return option.value == this.houseTypeSelectorModels[index] ? 'active-item-label' : '';
            },
            makeActiveStatusOnElements() {
                document.querySelector('.item-label').classList.remove('active-item-label');
                this.selectorsIndexer.forEach(function (value, index) {
                    let optionKey = this.houseTypeSelectorModels[index];
                    let label = document.querySelector('label[for="' + value + '_' + optionKey + '"]').classList.add('active-item-label');
                }, this);
            },
            fixedNumberFormatting(number, to) {
                return number.toFixed(to);
            },
            formattedEnergyClassPosition() {
                if (this.energyClass.length < 2) {
                    return this.energyClass;
                }

                var c = this.energyClass.substring(0, 1);
                var n = this.energyClass.substring(1, 2);
                return c + '<sup>' + n + '</sup>';

            },
            calculationData() {
                return {'selectorsOnTypeNumber': this.houseTypeSelectorModels}
            },
            detectSelectedOptionKey(options) {
                return options.filter(option => option.selected == 1)[0];
            },
            detectSelectedOptionIndex(options) {
                let index = 0;
                options.forEach((option, indexOpt) => {

                    if (option.selected == 1) {

                        index = indexOpt;
                    }
                });
                return index;
            },
            selectedOptionValue(option) {
                return option.selected == option.value ? true : false;
            },
            getOptionTitle(typeKey, option) {
                return this.props.generated[typeKey].web_options['col' + option.value]
            },
            identifyOption(typeKey, option) {
                return typeKey + '_' + option.value;
            },

        },
    });



}


