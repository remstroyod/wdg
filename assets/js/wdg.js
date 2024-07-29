window.onload = function()
{
    let ratings = document.getElementsByClassName('rating');

    for (let i = 0; i < ratings.length; i++)
    {
        const value = ratings[i].getAttribute('data-rating');

        let instance = jSuites.rating(ratings[i],
            {
                value: value,
                tooltip: ['1', '2', '3', '4', '5'],
                onchange: function (e, val)
                {
                    fetch(wdg_ajax_handle.ajaxurl,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                action: 'wdg_rating',
                                post: e.getAttribute('data-post'),
                                value: val
                            })
                        })
                        .then(response => response.text())
                        .then(response => {

                        });
                }
            });

    }

}
