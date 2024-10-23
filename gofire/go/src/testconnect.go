package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"path/filepath"
	"strings"

	_ "github.com/nakagami/firebirdsql"
)

type Produto struct {
	IDPRODUTO int     `json:"idproduto"`
	EAN       string  `json:"ean"`
	DESCRICAO string  `json:"descricao"`
	PRECO     float64 `json:"preco"`
	ESTOQUE   float64 `json:"estoque"`
}

func produtosHandler(w http.ResponseWriter, r *http.Request) {
	// Obtendo o nome da base de dados a partir dos parâmetros da query
	dbName := r.URL.Query().Get("db")
	if dbName == "" {
		http.Error(w, "Parâmetro de base de dados não fornecido", http.StatusBadRequest)
		return
	}

	// Definindo o caminho da base de dados
	dbPath := filepath.Join("..", "..", "db", fmt.Sprintf("%s.FDB", strings.ToUpper(dbName)))
	absPath, err := filepath.Abs(dbPath)
	if err != nil {
		http.Error(w, fmt.Sprintf("Erro ao converter o caminho para absoluto: %v", err), http.StatusInternalServerError)
		return
	}
	connStr := fmt.Sprintf("sysdba:masterkey@localhost:3050/%s", absPath)

	log.Printf("Conectando ao banco de dados em: %s\n", connStr)

	db, err := sql.Open("firebirdsql", connStr)
	if err != nil {
		http.Error(w, fmt.Sprintf("Erro ao conectar ao banco de dados: %v", err), http.StatusInternalServerError)
		return
	}
	defer db.Close()

	if err := db.Ping(); err != nil {
		http.Error(w, fmt.Sprintf("Erro ao pingar o banco de dados: %v", err), http.StatusInternalServerError)
		return
	}
	log.Println("Conexão com o banco de dados estabelecida com sucesso.")

	rows, err := db.Query("SELECT IDPRODUTO, EAN, DESCRICAO, PRECO, ESTOQUE FROM PRODUTOS;")
	if err != nil {
		http.Error(w, fmt.Sprintf("Erro ao executar a consulta: %v", err), http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var produtos []Produto
	for rows.Next() {
		var p Produto
		err := rows.Scan(&p.IDPRODUTO, &p.EAN, &p.DESCRICAO, &p.PRECO, &p.ESTOQUE)
		if err != nil {
			log.Printf("Erro ao escanear linha: %v\n", err)
			continue
		}
		produtos = append(produtos, p)
	}

	log.Printf("Dados retornados!!! ")

	w.Header().Set("Content-Type", "application/json")
	if err := json.NewEncoder(w).Encode(produtos); err != nil {
		http.Error(w, fmt.Sprintf("Erro ao codificar JSON: %v", err), http.StatusInternalServerError)
		return
	}

	if err := rows.Err(); err != nil {
		http.Error(w, fmt.Sprintf("Erro ao fetchear sobre as linhas: %v", err), http.StatusInternalServerError)
		return
	}
}

func main() {
	http.HandleFunc("/produtos", produtosHandler)
	http.Handle("/", http.FileServer(http.Dir("static"))) // Serve arquivos estáticos

	log.Println("Servidor ouvindo na porta 8080...")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
