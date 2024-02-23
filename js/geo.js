

    async function getCloudflareJSON()
    {
        let data = await fetch('/cdn-cgi/trace').then(res=>res.text());
        let arr = data.trim().split('\n').map(e=>e.split('='));
        return Object.fromEntries(arr);
    }

    getCloudflareJSON().then((log) => {
        console.log(log['loc']);
        var popup = document.getElementById('spiky-geo--wrapper');
        $content = $('#spiky-geo--content p');
        $redirectButton = $('#redirectButton');
        $stayButton = $('#stayButton');
        var regions = php_vars['leftHand'];
        var unsplit = php_vars['rightHand'];
        var splitArray = [];
        for (let i = 0; i < unsplit.length; i++)
        {
            var x = unsplit[i].split(',\r\n');
            splitArray.push(x);
        }

        var wrongCountry = false;
        var siteLang = window.location.pathname;
        var splitSiteLang = siteLang.split('/');
        var langFilter = splitSiteLang.filter(function(el)
        {
            return el != "";
        });
        var thisSiteLanguage = langFilter[0];
        console.log(regions);
        var euIndex = regions.indexOf('EU');
        console.log(splitArray);
        console.log(euIndex);
        var onUSA = (thisSiteLanguage !== 'gb' && thisSiteLanguage !== 'eu');
        $stayText = "";
        if(onUSA)
        {
            $stayText = "Stay in USA";
        }
        else if(thisSiteLanguage === "gb")
        {
            $stayText = "Stay in UK";
        }
        else if(thisSiteLanguage === "eu")
        {
            $stayText = "Stay in EU";
        }

        if(log['loc'] === 'GB' && thisSiteLanguage !== "gb")
        {
            $content.text('You appear to be in the UK, please switch to the UK site for UK shipping.');
            $stayButton.text($stayText);
            $redirectButton.text("Go to UK site");
            popup.style.display = 'block';
        }
        else if(splitArray[euIndex].includes(log['loc']) && thisSiteLanguage !== "eu")
        {
            $content.text('You appear to be in the EU, please switch to the EU site for EU shipping.');
            $stayButton.text($stayText);
            $redirectButton.text("Go to EU site");
            popup.style.display = 'block';
        }
        else if (!onUSA)
        {
            $content.text('You appear to be in the US, please switch to the US site for US shipping.');
            $stayButton.text($stayText);
            $redirectButton.text("Go to US site");
            popup.style.display = 'block';
        }

    });