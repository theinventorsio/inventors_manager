let row = input.config()

let response = await fetch('https://aragonesforms.ddns.net/', {
    method: 'POST',
    body: JSON.stringify(row),
    headers: {
        'Content-Type': 'application/json',
        'Authentication': 'Bearer Token'
    },
});

console.log(await response.json());