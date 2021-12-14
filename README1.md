file ./api1.php
Liste : # task-lists
	[GET] parametri:
		‘title’ => Titolo Lista
		‘deleteList’ => se presente elimina la lista con il 'title’  specificato.
	Se non è presente ‘deleteList’ ritorna la Lista con il ‘title’ specificato.
	Se non è presente ‘deleteList’ e ‘title’ tutte le liste
	[POST] parametri:
		‘title’ => Titolo Lista
		‘updateList’ => se presente aggiorna la Lista con il ‘title’ specificato 
	Se non è presente ‘updateList’ crea una lista con la post inviata 
Task # tasks
	[GET] parametri:
		‘listTitle’ => Titolo della Lista <obbligatori>
		‘title’ => Titolo della task
		‘deleteTask’ => se presente elimina la task con il ‘title’ specificato
	Se non è presente ‘deleteTask’ ritorna la Task  con il ‘title’ specificato.
	Se non è presente ‘deleteTask’ e ‘title’ ritorna tutte le tasks della ‘listTitle’ specificata
	[POST] parametri:
		‘listTitle’ => Titolo della Lista <obbligatori>
		‘title’ => Titolo Task
		‘updatetask’ => se presente aggiorna la Task con il ‘title’ specificato 
	Se non è presente ‘updatetask’ crea una Task con la post inviata sulla ‘listTitle’ specificata

Esempio POST Task:
{
  "id": "RG8zSWwtb0hDa2NRc2lYVA",
  "title": "Prima task 1",
  "etag": "\"NjcwMTE2OTYz\"",
  "due": "2021-05-01T00:00:00.000Z", => per il salvataggio deve avere il formato specificato
  "completed": null,
  "deleted": null,
  "hidden": null,
  "notes": null,
  "status": "needsAction",
  "updated": "2021-05-01T12:39:47.000Z"
}

