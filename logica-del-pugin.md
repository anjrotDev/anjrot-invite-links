# Logicas Anjrot Invite Links

La idea de este plugin es poder enviar links de invitación a posibles clientes, suponiendo que coloquemos en las redes sociales por ejemplo una promo de entrada gratis a mi curso, y digo que voy a dar 30 cupos, alli es donde entra la logica del link de invitación el mismo lo podemos setear a que se pueda usar solo una vez por persona hasta que alcance el cupo máximo en este caso de 30, luego de que llega a 30 podemos configurar el link que vaya a otra pagina de nuestro sitio o incluso a una pagina externa. La página cuando llega al máximo permitido se bloquea y más nadie puede ver esa pagina incluso ya existe una implementación donde bloqueamos la pagina por completo para que tampoco puedan llegar a ella si no tiene el uuid otorgado en la creación del link.

en la parte del frontend tenemos un formulario el cual son bloques que se pueden crear desde el editor de bloques de wordpress, el mismo puede configurarse para que envie un mensaje o para que envie los datos recolectados a una api externa, para este caso tenemos en el panel de administración del plugin una sección para setear un endpoint hasta ahora bastante simple y un token que lo mandamos de tipo Bearer.

toda esa logica hasta este momento 17-10-2024 ya está funcionando.
