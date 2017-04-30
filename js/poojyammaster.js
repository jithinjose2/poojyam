

function getBoxCount(i,j){
    var line1 = $("[data='"+i+","+j+","+(i+1)+","+j+"']");
    var line2 = $("[data='"+i+","+j+","+i+","+(j+1)+"']");
    var line3 = $("[data='"+i+","+(j+1)+","+(i+1)+","+(j+1)+"']");
    var line4 = $("[data='"+(i+1)+","+j+","+(i+1)+","+(j+1)+"']");
    
    var count = 0;
    count += line1.hasClass("selected")?1:0;
    count += line2.hasClass("selected")?1:0;
    count += line3.hasClass("selected")?1:0;
    count += line4.hasClass("selected")?1:0;
    return count;
}

function clickBestof(of){
    var size = 7;
    for(i=0;i<7;++i){
        for(j=0;j<7;++j){
            if(getBoxCount(i,j)==of){
                console.log(i+","+j);
                return true;
            }
        }
    }
    return false;
}


if(!clickBestof(3)){
    if(!clickBestof(0)){
        if(!clickBestof(1)){
            if(!clickBestof(2)){
                console.log("Game ended no action required");
            }
        }    
    }
}