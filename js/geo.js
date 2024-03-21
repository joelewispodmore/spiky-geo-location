
    async function getCloudflareJSON()
    {
        let data = await fetch('/cdn-cgi/trace').then(res=>res.text());
        let arr = data.trim().split('\n').map(e=>e.split('='));
        return Object.fromEntries(arr);
    }

    getCloudflareJSON().then((log) => {
        var popup = document.getElementById('spiky-geo--wrapper');
        $content = $('#spiky-geo--content p');
        $redirectButton = $('#redirectButton');
        $stayButton = $('#stayButton');

        var regions = php_vars['leftHand'];
        var countries = php_vars['rightHand'];

        var redirect = false;
        var redirectLoc = "";

        let i = 0;
        while(i < regions.length)
        {
            if(countries[i].includes(log['loc']))
            {
                redirectLoc = regions[i];
                redirect = true;
                break;
            }
            i++;
        }
        if(redirectLoc)
        {
            if(redirectLoc == "GB")
            {
                redirectLoc = "UK";
            }
            $content.text('You appear to be in the ' + redirectLoc + '. \n' +
                            'Please visit the ' + redirectLoc +
                            ' site.');
            $redirectButton.text("Go to " + redirectLoc + " site");
            popup.style.display = 'block';
            $('.lightboxBg').css('display', 'block');
        }
    });