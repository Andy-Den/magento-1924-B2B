/**
 * Adobe Edge: symbol definitions
 */
(function($, Edge, compId){
//images folder
var im='images/';

var fonts = {};
   fonts['Roboto']='<link href=\'http://fonts.googleapis.com/css?family=Roboto\' rel=\'stylesheet\' type=\'text/css\'>';


var resources = [
];
var symbols = {
"stage": {
   version: "2.0.1",
   minimumCompatibleVersion: "2.0.0",
   build: "2.0.1.268",
   baseState: "Base State",
   initialState: "Base State",
   gpuAccelerate: false,
   resizeInstances: false,
   content: {
         dom: [
         {
            id:'background',
            type:'image',
            rect:['39px','21px','398px','248px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"background.png",'0px','0px']
         },
         {
            id:'Shelvings_-_empty',
            type:'image',
            rect:['0','31px','475px','259px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Shelvings%20-%20empty.png",'0px','0px']
         },
         {
            id:'Raff',
            type:'image',
            tag:'img',
            rect:['172px','91px','133px','224px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Raff.png",'0px','0px']
         },
         {
            id:'Raff_2',
            display:'none',
            type:'image',
            rect:['164px','53px','171px','280px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Raff_2.png",'0px','0px']
         },
         {
            id:'Product1',
            display:'none',
            type:'image',
            rect:['-209px','31px','85px','46px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product12.png",'0px','0px']
         },
         {
            id:'Product2',
            display:'none',
            type:'image',
            rect:['-209px','31px','85px','46px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product12.png",'0px','0px']
         },
         {
            id:'Product3',
            display:'none',
            type:'image',
            rect:['0','0','76px','47px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product3.png",'0px','0px']
         },
         {
            id:'Product4',
            display:'none',
            type:'image',
            rect:['0','0','76px','47px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product3.png",'0px','0px']
         },
         {
            id:'Product5',
            display:'none',
            type:'image',
            rect:['0','0','93px','29px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product5.png",'0px','0px']
         },
         {
            id:'Product6',
            display:'none',
            type:'image',
            rect:['0','0','93px','29px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product5.png",'0px','0px']
         },
         {
            id:'Product7',
            display:'none',
            type:'image',
            rect:['0','0','76px','44px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product7.png",'0px','0px']
         },
         {
            id:'Product8',
            display:'none',
            type:'image',
            rect:['0','0','69px','52px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Product8.png",'0px','0px']
         },
         {
            id:'Raff_Sad',
            display:'none',
            type:'image',
            rect:['212px','116px','36px','45px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Raff%20Sad.png",'0px','0px']
         },
         {
            id:'Raff_Happy',
            display:'none',
            type:'image',
            rect:['208px','116px','50px','53px','auto','auto'],
            opacity:1,
            fill:["rgba(0,0,0,0)",im+"Raff%20Happy.png",'0px','0px']
         },
         {
            id:'Button',
            display:'none',
            type:'image',
            rect:['61px','86px','355px','130px','auto','auto'],
            opacity:0.390625,
            fill:["rgba(0,0,0,0)",im+"Button.png",'0px','0px']
         },
         {
            id:'Click',
            display:'none',
            type:'image',
            rect:['252px','177px','91px','69px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Click.png",'0px','0px']
         },
         {
            id:'computer',
            display:'none',
            type:'image',
            rect:['58px','63px','362px','157px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"computer.png",'0px','0px']
         },
         {
            id:'Lamp',
            type:'image',
            rect:['289px','70px','32px','31px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"Lamp.png",'0px','0px']
         },
         {
            id:'computer2',
            type:'image',
            rect:['0px','0px','475px','375px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"computer2.png",'0px','0px']
         },
         {
            id:'mouse',
            display:'none',
            type:'image',
            rect:['310px','196px','108px','103px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"mouse.png",'0px','0px']
         },
         {
            id:'Text_5',
            display:'none',
            type:'text',
            rect:['14px','314px','449px','45px','auto','auto'],
            text:"Dr. Dog está satisfeito, agora ele tem todos os \rprodutos para atender seus clientes!",
            align:"left",
            font:['Roboto',18,"rgba(0,0,0,1)","normal","none",""]
         },
         {
            id:'layer',
            display:'none',
            type:'image',
            rect:['0','0','475px','375px','auto','auto'],
            fill:["rgba(0,0,0,0)",im+"layer.png",'0px','0px']
         },
         {
            id:'Rectangle',
            display:'none',
            type:'rect',
            rect:['51px','131px','374px','139px','auto','auto'],
            cursor:['pointer'],
            fill:["rgba(255,0,0,0.00)"],
            stroke:[0,"rgba(0, 0, 0, 0)","none"]
         },
         {
            id:'Text_2',
            display:'none',
            type:'text',
            rect:['14px','314px','449px','45px','auto','auto'],
            text:"E então ele conheceu a 4vets!",
            align:"center",
            font:['Roboto',18,"rgba(0,0,0,1)","normal","none",""]
         },
         {
            id:'Text_1',
            type:'text',
            rect:['27px','314px','436px','45px','auto','auto'],
            text:"Dr. Dog queria fazer as compras do seu pet shop pela internet...",
            align:"left",
            font:['Roboto',18,"rgba(62,64,64,1.00)","normal","none",""]
         },
         {
            id:'Ellipse',
            display:'none',
            type:'ellipse',
            rect:['2px','309px','278px','67px','auto','auto'],
            cursor:['pointer'],
            borderRadius:["50%","50%","50%","50%"],
            fill:["rgba(192,192,192,0.00)"],
            stroke:[0,"rgba(0,0,0,1)","none"]
         }],
         symbolInstances: [

         ]
      },
   states: {
      "Base State": {
         "${_Product7}": [
            ["style", "top", '254px'],
            ["transform", "scaleY", '3'],
            ["style", "display", 'none'],
            ["style", "left", '551px'],
            ["transform", "scaleX", '3']
         ],
         "${_Shelvings_-_empty}": [
            ["style", "top", '37px'],
            ["transform", "scaleX", '1'],
            ["transform", "scaleY", '1'],
            ["style", "display", 'block'],
            ["style", "height", '272px'],
            ["style", "opacity", '0'],
            ["style", "left", '2px'],
            ["style", "width", '470px']
         ],
         "${_Click}": [
            ["style", "top", '177px'],
            ["style", "height", '69px'],
            ["style", "display", 'none'],
            ["style", "opacity", '1'],
            ["style", "left", '252px'],
            ["style", "width", '91px']
         ],
         "${_Product6}": [
            ["style", "top", '30px'],
            ["transform", "scaleY", '3'],
            ["style", "display", 'none'],
            ["style", "left", '-190px'],
            ["transform", "scaleX", '-3']
         ],
         "${_Product1}": [
            ["style", "top", '-119px'],
            ["transform", "scaleY", '3'],
            ["transform", "scaleX", '3'],
            ["style", "left", '591px'],
            ["style", "display", 'none']
         ],
         "${_Lamp}": [
            ["style", "top", '9px'],
            ["transform", "scaleX", '0.37895'],
            ["transform", "scaleY", '0.37895'],
            ["style", "display", 'block'],
            ["style", "height", '92px'],
            ["style", "opacity", '0'],
            ["style", "left", '232px'],
            ["style", "width", '95px']
         ],
         "${_Product4}": [
            ["style", "top", '201px'],
            ["transform", "scaleY", '3'],
            ["style", "display", 'none'],
            ["style", "left", '-159px'],
            ["transform", "scaleX", '-3']
         ],
         "${_Raff_Sad}": [
            ["style", "top", '125px'],
            ["style", "display", 'none'],
            ["style", "height", '1px'],
            ["style", "opacity", '0'],
            ["style", "left", '224px'],
            ["style", "width", '26px']
         ],
         "${_Rectangle}": [
            ["style", "display", 'none'],
            ["style", "cursor", 'pointer'],
            ["color", "background-color", 'rgba(255,0,0,0.00)']
         ],
         "${_Raff}": [
            ["style", "top", '63px'],
            ["style", "display", 'block'],
            ["transform", "scaleY", '1'],
            ["transform", "scaleX", '1'],
            ["style", "height", '263px'],
            ["style", "opacity", '0'],
            ["style", "left", '168px'],
            ["style", "width", '137px']
         ],
         "${_Text_5}": [
            ["style", "top", '314px'],
            ["style", "font-size", '18px'],
            ["style", "text-align", 'left'],
            ["style", "display", 'none'],
            ["style", "height", '45px'],
            ["style", "font-family", 'Roboto'],
            ["style", "left", '14px'],
            ["style", "width", '449px']
         ],
         "${_mouse}": [
            ["style", "top", '196px'],
            ["style", "display", 'none'],
            ["style", "height", '103px'],
            ["style", "opacity", '0'],
            ["style", "left", '310px'],
            ["style", "width", '108px']
         ],
         "${_Raff_2}": [
            ["style", "top", '53px'],
            ["style", "display", 'none'],
            ["style", "height", '420px'],
            ["style", "opacity", '0'],
            ["style", "left", '72px'],
            ["style", "width", '257px']
         ],
         "${_Product8}": [
            ["style", "top", '225px'],
            ["transform", "scaleY", '3'],
            ["transform", "scaleX", '3'],
            ["style", "left", '-136px'],
            ["style", "display", 'none']
         ],
         "${_Product2}": [
            ["style", "top", '-119px'],
            ["transform", "scaleY", '3'],
            ["transform", "scaleX", '-3'],
            ["style", "left", '-189px'],
            ["style", "display", 'none']
         ],
         "${_Text_2}": [
            ["style", "top", '314px'],
            ["style", "width", '449px'],
            ["style", "text-align", 'center'],
            ["style", "height", '45px'],
            ["style", "display", 'none'],
            ["style", "font-family", 'Roboto'],
            ["style", "left", '14px'],
            ["style", "font-size", '18px']
         ],
         "${_Product5}": [
            ["style", "top", '30px'],
            ["transform", "scaleY", '3'],
            ["style", "display", 'none'],
            ["style", "left", '570px'],
            ["transform", "scaleX", '3']
         ],
         "${_background}": [
            ["style", "top", '21px'],
            ["style", "left", '39px']
         ],
         "${_Button}": [
            ["style", "top", '86px'],
            ["style", "height", '130px'],
            ["style", "display", 'none'],
            ["style", "opacity", '0'],
            ["style", "left", '61px'],
            ["style", "width", '355px']
         ],
         "${_Raff_Happy}": [
            ["style", "top", '120px'],
            ["style", "display", 'none'],
            ["transform", "scaleY", '1'],
            ["transform", "scaleX", '1.00301'],
            ["style", "height", '45px'],
            ["style", "opacity", '0'],
            ["style", "left", '211px'],
            ["style", "width", '43px']
         ],
         "${_layer}": [
            ["style", "display", 'none']
         ],
         "${_Ellipse}": [
            ["color", "background-color", 'rgba(192,192,192,0.00)'],
            ["style", "top", '309px'],
            ["style", "left", '2px'],
            ["style", "height", '67px'],
            ["style", "display", 'none'],
            ["style", "cursor", 'pointer'],
            ["style", "width", '278px']
         ],
         "${_computer}": [
            ["style", "top", '63px'],
            ["style", "display", 'none'],
            ["style", "height", '157px'],
            ["style", "opacity", '0'],
            ["style", "left", '58px'],
            ["style", "width", '362px']
         ],
         "${_Stage}": [
            ["color", "background-color", 'rgba(255,255,255,1)'],
            ["style", "width", '475px'],
            ["style", "height", '375px'],
            ["style", "overflow", 'hidden']
         ],
         "${_computer2}": [
            ["style", "left", '0px'],
            ["style", "top", '0px']
         ],
         "${_Product3}": [
            ["style", "top", '201px'],
            ["transform", "scaleY", '3'],
            ["style", "display", 'none'],
            ["style", "left", '561px'],
            ["transform", "scaleX", '3']
         ],
         "${_Text_1}": [
            ["color", "color", 'rgba(62,64,64,1.00)'],
            ["style", "left", '27px'],
            ["style", "font-size", '18px'],
            ["style", "top", '314px'],
            ["style", "text-align", 'left'],
            ["style", "display", 'block'],
            ["style", "font-family", 'Roboto'],
            ["style", "height", '45px'],
            ["style", "width", '436px']
         ]
      }
   },
   timelines: {
      "Default Timeline": {
         fromState: "Base State",
         toState: "",
         duration: 21500,
         autoPlay: true,
         timeline: [
            { id: "eid464", tween: [ "transform", "${_Product1}", "scaleX", '1', { fromValue: '3'}], position: 17000, duration: 1000 },
            { id: "eid333", tween: [ "style", "${_Raff_Happy}", "left", '226px', { fromValue: '211px'}], position: 0, duration: 0 },
            { id: "eid167", tween: [ "style", "${_Raff_Happy}", "left", '162px', { fromValue: '226px'}], position: 5000, duration: 1677 },
            { id: "eid332", tween: [ "style", "${_Shelvings_-_empty}", "top", '19px', { fromValue: '37px'}], position: 0, duration: 0 },
            { id: "eid70", tween: [ "style", "${_Shelvings_-_empty}", "top", '-276px', { fromValue: '19px'}], position: 5000, duration: 1677 },
            { id: "eid306", tween: [ "style", "${_Shelvings_-_empty}", "top", '16px', { fromValue: '-276px'}], position: 15500, duration: 2500 },
            { id: "eid554", tween: [ "style", "${_Raff_Happy}", "display", 'none', { fromValue: 'none'}], position: 0, duration: 0 },
            { id: "eid198", tween: [ "style", "${_Raff_Happy}", "display", 'none', { fromValue: 'none'}], position: 5000, duration: 0 },
            { id: "eid551", tween: [ "style", "${_Raff_Happy}", "display", 'block', { fromValue: 'none'}], position: 6677, duration: 0 },
            { id: "eid184", tween: [ "style", "${_Raff_Happy}", "display", 'block', { fromValue: 'block'}], position: 10000, duration: 0 },
            { id: "eid235", tween: [ "style", "${_Raff_Happy}", "display", 'none', { fromValue: 'block'}], position: 10500, duration: 0 },
            { id: "eid239", tween: [ "style", "${_Raff_Happy}", "display", 'none', { fromValue: 'none'}], position: 15000, duration: 0 },
            { id: "eid492", tween: [ "style", "${_Product5}", "display", 'block', { fromValue: 'none'}], position: 18000, duration: 0 },
            { id: "eid466", tween: [ "style", "${_Product1}", "left", '65px', { fromValue: '591px'}], position: 17000, duration: 1000 },
            { id: "eid491", tween: [ "style", "${_Product4}", "display", 'block', { fromValue: 'none'}], position: 17731, duration: 0 },
            { id: "eid215", tween: [ "style", "${_Button}", "display", 'block', { fromValue: 'none'}], position: 12500, duration: 0 },
            { id: "eid445", tween: [ "style", "${_Button}", "display", 'none', { fromValue: 'block'}], position: 15500, duration: 0 },
            { id: "eid337", tween: [ "style", "${_Raff_Happy}", "width", '25px', { fromValue: '43px'}], position: 0, duration: 0 },
            { id: "eid426", tween: [ "style", "${_Raff_Happy}", "width", '46px', { fromValue: '25px'}], position: 5000, duration: 1677 },
            { id: "eid558", tween: [ "style", "${_Raff_Sad}", "opacity", '1', { fromValue: '0'}], position: 1000, duration: 1000 },
            { id: "eid265", tween: [ "style", "${_Lamp}", "top", '-61px', { fromValue: '9px'}], position: 5000, duration: 1677 },
            { id: "eid27", tween: [ "style", "${_Text_1}", "display", 'none', { fromValue: 'block'}], position: 5000, duration: 0 },
            { id: "eid183", tween: [ "style", "${_Text_1}", "display", 'none', { fromValue: 'none'}], position: 7500, duration: 0 },
            { id: "eid453", tween: [ "style", "${_Product1}", "display", 'block', { fromValue: 'none'}], position: 17000, duration: 0 },
            { id: "eid273", tween: [ "style", "${_Lamp}", "width", '264px', { fromValue: '95px'}], position: 5000, duration: 1677 },
            { id: "eid533", tween: [ "transform", "${_Product8}", "scaleY", '1', { fromValue: '3'}], position: 18725, duration: 1000 },
            { id: "eid401", tween: [ "style", "${_Raff_Sad}", "top", '125px', { fromValue: '125px'}], position: 2000, duration: 0 },
            { id: "eid76", tween: [ "style", "${_Raff_Sad}", "top", '182px', { fromValue: '125px'}], position: 5000, duration: 1677 },
            { id: "eid486", tween: [ "transform", "${_Product3}", "scaleY", '1', { fromValue: '3'}], position: 17500, duration: 1000 },
            { id: "eid470", tween: [ "transform", "${_Product2}", "scaleY", '1', { fromValue: '3'}], position: 17250, duration: 987 },
            { id: "eid506", tween: [ "style", "${_Product6}", "top", '180px', { fromValue: '30px'}], position: 18237, duration: 1000 },
            { id: "eid51", tween: [ "style", "${_Lamp}", "opacity", '0', { fromValue: '0'}], position: 0, duration: 0 },
            { id: "eid53", tween: [ "style", "${_Lamp}", "opacity", '1', { fromValue: '0.000000'}], position: 5000, duration: 1677 },
            { id: "eid203", tween: [ "style", "${_Lamp}", "opacity", '0', { fromValue: '1'}], position: 10000, duration: 500 },
            { id: "eid608", tween: [ "style", "${_layer}", "display", 'block', { fromValue: 'none'}], position: 21500, duration: 0 },
            { id: "eid487", tween: [ "style", "${_Product4}", "top", '113px', { fromValue: '201px'}], position: 17731, duration: 994 },
            { id: "eid467", tween: [ "style", "${_Product1}", "top", '60px', { fromValue: '-119px'}], position: 17000, duration: 1000 },
            { id: "eid563", tween: [ "style", "${_Ellipse}", "display", 'block', { fromValue: 'none'}], position: 21500, duration: 0 },
            { id: "eid485", tween: [ "transform", "${_Product3}", "scaleX", '1', { fromValue: '3'}], position: 17500, duration: 1000 },
            { id: "eid604", tween: [ "style", "${_Raff_2}", "opacity", '1', { fromValue: '0'}], position: 15000, duration: 500 },
            { id: "eid14", tween: [ "style", "${_Raff}", "opacity", '0', { fromValue: '0.000000'}], position: 0, duration: 1000 },
            { id: "eid21", tween: [ "style", "${_Raff}", "opacity", '1', { fromValue: '0'}], position: 1000, duration: 1000 },
            { id: "eid199", tween: [ "style", "${_Raff}", "opacity", '1', { fromValue: '1'}], position: 2000, duration: 0 },
            { id: "eid200", tween: [ "style", "${_Raff}", "opacity", '0', { fromValue: '1'}], position: 10000, duration: 500 },
            { id: "eid233", tween: [ "style", "${_Raff}", "opacity", '0', { fromValue: '0'}], position: 10500, duration: 0 },
            { id: "eid238", tween: [ "style", "${_Raff}", "opacity", '1', { fromValue: '0'}], position: 15000, duration: 500 },
            { id: "eid520", tween: [ "style", "${_Product7}", "top", '235px', { fromValue: '254px'}], position: 18500, duration: 1000 },
            { id: "eid474", tween: [ "style", "${_Product3}", "display", 'block', { fromValue: 'none'}], position: 17500, duration: 0 },
            { id: "eid507", tween: [ "transform", "${_Product6}", "scaleY", '1', { fromValue: '3'}], position: 18237, duration: 1000 },
            { id: "eid263", tween: [ "style", "${_Lamp}", "display", 'none', { fromValue: 'block'}], position: 5000, duration: 0 },
            { id: "eid562", tween: [ "style", "${_Lamp}", "display", 'block', { fromValue: 'none'}], position: 6677, duration: 0 },
            { id: "eid192", tween: [ "style", "${_Lamp}", "display", 'block', { fromValue: 'block'}], position: 10000, duration: 0 },
            { id: "eid188", tween: [ "style", "${_Lamp}", "display", 'none', { fromValue: 'block'}], position: 12500, duration: 0 },
            { id: "eid600", tween: [ "style", "${_Raff_2}", "top", '53px', { fromValue: '53px'}], position: 15500, duration: 0 },
            { id: "eid594", tween: [ "style", "${_Raff_2}", "top", '53px', { fromValue: '53px'}], position: 18000, duration: 0 },
            { id: "eid596", tween: [ "style", "${_Raff_2}", "width", '171px', { fromValue: '257px'}], position: 15500, duration: 2500 },
            { id: "eid329", tween: [ "style", "${_Raff}", "left", '168px', { fromValue: '168px'}], position: 0, duration: 0 },
            { id: "eid65", tween: [ "style", "${_Raff}", "left", '73px', { fromValue: '168px'}], position: 5000, duration: 1677 },
            { id: "eid579", tween: [ "style", "${_Raff}", "left", '160px', { fromValue: '73px'}], position: 15500, duration: 2500 },
            { id: "eid268", tween: [ "transform", "${_Lamp}", "scaleX", '0.37895', { fromValue: '0.37895'}], position: 5000, duration: 0 },
            { id: "eid521", tween: [ "transform", "${_Product7}", "scaleX", '1', { fromValue: '3'}], position: 18500, duration: 1000 },
            { id: "eid272", tween: [ "style", "${_Lamp}", "height", '276px', { fromValue: '92px'}], position: 5000, duration: 1677 },
            { id: "eid220", tween: [ "style", "${_mouse}", "opacity", '1', { fromValue: '0'}], position: 12500, duration: 500 },
            { id: "eid227", tween: [ "style", "${_mouse}", "opacity", '0', { fromValue: '1'}], position: 15000, duration: 500 },
            { id: "eid483", tween: [ "style", "${_Product3}", "left", '65px', { fromValue: '561px'}], position: 17500, duration: 1000 },
            { id: "eid510", tween: [ "style", "${_Product6}", "display", 'block', { fromValue: 'none'}], position: 18237, duration: 0 },
            { id: "eid509", tween: [ "style", "${_Product6}", "left", '313px', { fromValue: '-190px'}], position: 18237, duration: 1000 },
            { id: "eid469", tween: [ "style", "${_Product2}", "top", '60px', { fromValue: '-119px'}], position: 17250, duration: 987 },
            { id: "eid530", tween: [ "style", "${_Product8}", "left", '334px', { fromValue: '-136px'}], position: 18725, duration: 1000 },
            { id: "eid512", tween: [ "style", "${_Product7}", "display", 'block', { fromValue: 'none'}], position: 18500, duration: 0 },
            { id: "eid605", tween: [ "style", "${_Raff_2}", "display", 'block', { fromValue: 'none'}], position: 15000, duration: 0 },
            { id: "eid218", tween: [ "style", "${_mouse}", "display", 'block', { fromValue: 'none'}], position: 12500, duration: 0 },
            { id: "eid511", tween: [ "style", "${_mouse}", "display", 'none', { fromValue: 'block'}], position: 15500, duration: 0 },
            { id: "eid532", tween: [ "transform", "${_Product8}", "scaleX", '1', { fromValue: '3'}], position: 18725, duration: 1000 },
            { id: "eid471", tween: [ "style", "${_Product2}", "display", 'block', { fromValue: 'none'}], position: 17731, duration: 0 },
            { id: "eid400", tween: [ "style", "${_Raff_Sad}", "left", '224px', { fromValue: '224px'}], position: 2000, duration: 0 },
            { id: "eid77", tween: [ "style", "${_Raff_Sad}", "left", '157px', { fromValue: '224px'}], position: 5000, duration: 1677 },
            { id: "eid465", tween: [ "transform", "${_Product1}", "scaleY", '1', { fromValue: '3'}], position: 17000, duration: 1000 },
            { id: "eid243", tween: [ "style", "${_Text_5}", "display", 'block', { fromValue: 'none'}], position: 15000, duration: 0 },
            { id: "eid420", tween: [ "style", "${_Raff_Happy}", "opacity", '1', { fromValue: '0'}], position: 5000, duration: 1677 },
            { id: "eid197", tween: [ "style", "${_Raff_Happy}", "opacity", '0', { fromValue: '1'}], position: 10000, duration: 500 },
            { id: "eid30", tween: [ "style", "${_Text_2}", "display", 'none', { fromValue: 'none'}], position: 0, duration: 0 },
            { id: "eid28", tween: [ "style", "${_Text_2}", "display", 'block', { fromValue: 'none'}], position: 5000, duration: 0 },
            { id: "eid182", tween: [ "style", "${_Text_2}", "display", 'none', { fromValue: 'block'}], position: 10000, duration: 0 },
            { id: "eid269", tween: [ "transform", "${_Lamp}", "scaleY", '0.37895', { fromValue: '0.37895'}], position: 5000, duration: 0 },
            { id: "eid609", tween: [ "style", "${_Rectangle}", "display", 'block', { fromValue: 'none'}], position: 21500, duration: 0 },
            { id: "eid522", tween: [ "transform", "${_Product7}", "scaleY", '1', { fromValue: '3'}], position: 18500, duration: 1000 },
            { id: "eid598", tween: [ "style", "${_Raff_2}", "height", '280px', { fromValue: '420px'}], position: 15500, duration: 2500 },
            { id: "eid24", tween: [ "style", "${_Raff}", "display", 'block', { fromValue: 'block'}], position: 5000, duration: 0 },
            { id: "eid186", tween: [ "style", "${_Raff}", "display", 'block', { fromValue: 'block'}], position: 10000, duration: 0 },
            { id: "eid236", tween: [ "style", "${_Raff}", "display", 'none', { fromValue: 'block'}], position: 10500, duration: 0 },
            { id: "eid237", tween: [ "style", "${_Raff}", "display", 'none', { fromValue: 'none'}], position: 15000, duration: 0 },
            { id: "eid505", tween: [ "transform", "${_Product5}", "scaleY", '1', { fromValue: '3'}], position: 18000, duration: 1000 },
            { id: "eid531", tween: [ "style", "${_Product8}", "top", '220px', { fromValue: '225px'}], position: 18725, duration: 1000 },
            { id: "eid490", tween: [ "style", "${_Product4}", "left", '323px', { fromValue: '-159px'}], position: 17731, duration: 994 },
            { id: "eid523", tween: [ "style", "${_Product8}", "display", 'block', { fromValue: 'none'}], position: 18725, duration: 0 },
            { id: "eid25", tween: [ "style", "${_Shelvings_-_empty}", "display", 'block', { fromValue: 'block'}], position: 5000, duration: 0 },
            { id: "eid187", tween: [ "style", "${_Shelvings_-_empty}", "display", 'none', { fromValue: 'block'}], position: 10000, duration: 0 },
            { id: "eid446", tween: [ "style", "${_Shelvings_-_empty}", "display", 'block', { fromValue: 'none'}], position: 15500, duration: 0 },
            { id: "eid340", tween: [ "style", "${_Raff_Happy}", "height", '20px', { fromValue: '45px'}], position: 0, duration: 0 },
            { id: "eid165", tween: [ "style", "${_Raff_Happy}", "height", '31px', { fromValue: '20px'}], position: 5000, duration: 1677 },
            { id: "eid330", tween: [ "style", "${_Raff}", "top", '45px', { fromValue: '63px'}], position: 0, duration: 0 },
            { id: "eid408", tween: [ "style", "${_Raff}", "top", '52px', { fromValue: '45px'}], position: 5000, duration: 1677 },
            { id: "eid580", tween: [ "style", "${_Raff}", "top", '53px', { fromValue: '52px'}], position: 15500, duration: 2500 },
            { id: "eid223", tween: [ "style", "${_Click}", "display", 'block', { fromValue: 'none'}], position: 13346, duration: 0 },
            { id: "eid331", tween: [ "style", "${_Shelvings_-_empty}", "left", '2px', { fromValue: '2px'}], position: 0, duration: 0 },
            { id: "eid71", tween: [ "style", "${_Shelvings_-_empty}", "left", '-600px', { fromValue: '2px'}], position: 5000, duration: 1677 },
            { id: "eid374", tween: [ "style", "${_Shelvings_-_empty}", "left", '2px', { fromValue: '-600px'}], position: 15500, duration: 2500 },
            { id: "eid473", tween: [ "transform", "${_Product2}", "scaleX", '-1', { fromValue: '-3'}], position: 17250, duration: 987 },
            { id: "eid503", tween: [ "style", "${_Product5}", "top", '185px', { fromValue: '30px'}], position: 18000, duration: 1000 },
            { id: "eid334", tween: [ "style", "${_Raff_Happy}", "top", '134px', { fromValue: '120px'}], position: 0, duration: 0 },
            { id: "eid168", tween: [ "style", "${_Raff_Happy}", "top", '168px', { fromValue: '134px'}], position: 5000, duration: 1677 },
            { id: "eid13", tween: [ "style", "${_Shelvings_-_empty}", "opacity", '1', { fromValue: '0.000000'}], position: 0, duration: 2000 },
            { id: "eid191", tween: [ "style", "${_computer}", "display", 'block', { fromValue: 'none'}], position: 10000, duration: 0 },
            { id: "eid447", tween: [ "style", "${_computer}", "display", 'none', { fromValue: 'block'}], position: 13000, duration: 0 },
            { id: "eid336", tween: [ "style", "${_Shelvings_-_empty}", "width", '470px', { fromValue: '470px'}], position: 0, duration: 0 },
            { id: "eid59", tween: [ "style", "${_Shelvings_-_empty}", "width", '1678px', { fromValue: '470px'}], position: 5000, duration: 1677 },
            { id: "eid370", tween: [ "style", "${_Shelvings_-_empty}", "width", '470px', { fromValue: '1678px'}], position: 15500, duration: 2500 },
            { id: "eid229", tween: [ "style", "${_Click}", "opacity", '0', { fromValue: '1'}], position: 15000, duration: 500 },
            { id: "eid504", tween: [ "transform", "${_Product5}", "scaleX", '1', { fromValue: '3'}], position: 18000, duration: 1000 },
            { id: "eid590", tween: [ "style", "${_Raff_Sad}", "height", '3px', { fromValue: '1px'}], position: 5000, duration: 1677 },
            { id: "eid601", tween: [ "style", "${_Raff_2}", "left", '164px', { fromValue: '72px'}], position: 15500, duration: 2500 },
            { id: "eid488", tween: [ "transform", "${_Product4}", "scaleY", '1', { fromValue: '3'}], position: 17731, duration: 994 },
            { id: "eid472", tween: [ "style", "${_Product2}", "left", '317px', { fromValue: '-189px'}], position: 17250, duration: 987 },
            { id: "eid264", tween: [ "style", "${_Lamp}", "left", '222px', { fromValue: '232px'}], position: 5000, duration: 1677 },
            { id: "eid195", tween: [ "style", "${_computer}", "opacity", '1', { fromValue: '0'}], position: 10000, duration: 500 },
            { id: "eid211", tween: [ "style", "${_computer}", "opacity", '1', { fromValue: '1'}], position: 10500, duration: 0 },
            { id: "eid212", tween: [ "style", "${_computer}", "opacity", '0', { fromValue: '1'}], position: 12500, duration: 500 },
            { id: "eid55", tween: [ "style", "${_Raff}", "width", '223px', { fromValue: '137px'}], position: 5000, duration: 1677 },
            { id: "eid581", tween: [ "style", "${_Raff}", "width", '171px', { fromValue: '223px'}], position: 15500, duration: 2500 },
            { id: "eid338", tween: [ "style", "${_Raff}", "height", '263px', { fromValue: '263px'}], position: 0, duration: 0 },
            { id: "eid54", tween: [ "style", "${_Raff}", "height", '420px', { fromValue: '263px'}], position: 5000, duration: 1677 },
            { id: "eid582", tween: [ "style", "${_Raff}", "height", '280px', { fromValue: '420px'}], position: 15500, duration: 2500 },
            { id: "eid508", tween: [ "transform", "${_Product6}", "scaleX", '-1', { fromValue: '-3'}], position: 18237, duration: 1000 },
            { id: "eid217", tween: [ "style", "${_Button}", "opacity", '1', { fromValue: '0'}], position: 12500, duration: 500 },
            { id: "eid225", tween: [ "style", "${_Button}", "opacity", '0', { fromValue: '1'}], position: 15000, duration: 500 },
            { id: "eid489", tween: [ "transform", "${_Product4}", "scaleX", '-1', { fromValue: '-3'}], position: 17731, duration: 994 },
            { id: "eid555", tween: [ "style", "${_Raff_Sad}", "display", 'none', { fromValue: 'none'}], position: 0, duration: 0 },
            { id: "eid556", tween: [ "style", "${_Raff_Sad}", "display", 'block', { fromValue: 'none'}], position: 1000, duration: 0 },
            { id: "eid23", tween: [ "style", "${_Raff_Sad}", "display", 'block', { fromValue: 'block'}], position: 5000, duration: 0 },
            { id: "eid552", tween: [ "style", "${_Raff_Sad}", "display", 'none', { fromValue: 'block'}], position: 6677, duration: 0 },
            { id: "eid185", tween: [ "style", "${_Raff_Sad}", "display", 'none', { fromValue: 'none'}], position: 10000, duration: 0 },
            { id: "eid502", tween: [ "style", "${_Product5}", "left", '60px', { fromValue: '570px'}], position: 18000, duration: 1000 },
            { id: "eid484", tween: [ "style", "${_Product3}", "top", '116px', { fromValue: '201px'}], position: 17500, duration: 1000 },
            { id: "eid519", tween: [ "style", "${_Product7}", "left", '61px', { fromValue: '551px'}], position: 18500, duration: 1000 },
            { id: "eid339", tween: [ "style", "${_Shelvings_-_empty}", "height", '272px', { fromValue: '272px'}], position: 0, duration: 0 },
            { id: "eid58", tween: [ "style", "${_Shelvings_-_empty}", "height", '913px', { fromValue: '272px'}], position: 5000, duration: 1677 },
            { id: "eid369", tween: [ "style", "${_Shelvings_-_empty}", "height", '272px', { fromValue: '913px'}], position: 15500, duration: 2500 },
            { id: "eid415", tween: [ "style", "${_Raff_Sad}", "width", '57px', { fromValue: '26px'}], position: 5000, duration: 1677 }         ]
      }
   }
}
};


Edge.registerCompositionDefn(compId, symbols, fonts, resources);

/**
 * Adobe Edge DOM Ready Event Handler
 */
$(window).ready(function() {
     Edge.launchComposition(compId);
});
})(jQuery, AdobeEdge, "EDGE-62982752");
