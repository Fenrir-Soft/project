const form = document.querySelector('form[name=login-form]')
if (form) {
    form.addEventListener('submit', async (event) => { 
        event.stopPropagation();
        event.preventDefault();

        try {

            const response = await fetch('sign-in', {
                method: 'POST',
                body: new FormData(form),                
            })
            const { data } = await response.json()

            if (!response.ok) {
                throw new Error(data?.error||"Username or password is invalid")
            }

            location.href = 'protected'
            
        } catch (error) {
            alert(error.message)
        }
    });
}