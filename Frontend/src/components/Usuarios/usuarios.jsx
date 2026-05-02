import { use, useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";

import DadosTable from "../Table/table";
import UsuarioModal from "../Modais/Usuario/modalUsuario";
import { toast } from "react-toastify";


function Usuarios() {
  const [usuarios, setUsuarios] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [showModalDeletar, setShowModalDeletar] = useState(false);
  const [dadosForm, setDadosForm] = useState(null);
  const [editando, setEditando] = useState(false)

  const buscarUsuarios = async () => {
    try {
      const res = await Api.get("/usuario");

      setUsuarios(res.data.dados);
      console.log(res.data.dados);
    } catch (err) {
      toast.error('Erro ao buscar usuários', {
        position: "top-right",
        autoClose: 3000,
      });
      console.log(err);
    }
  };

  useEffect(() => {
    buscarUsuarios();
  }, []);

  
  const handleEdit = (row) => {
    console.log("Editando", row);
    setDadosForm(row)
    setShowModal(true)
  };

  const handleDelete = (id) => {
    console.log("Excluindo", id);
    setShowModalDeletar(true)
  };



  const columns = [
    { header: "Nome", accessor: "nome" },
    { header: "Email", accessor: "email" },
    { header: "Cpf", accessor: "cpf" },
    { header: "Cargo", accessor: "cargo" },
    {
      header: "Acoes",
      accessor: "acoes",
      render: (row) => (
        <div className="d-flex gap-2">
          <Button
            className="ignorar-fonte-btn"
            variant="warning"
            size="sm"
            onClick={() => handleEdit(row)}
          >
            <CiEdit />
          </Button>
          <Button
            className="ignorar-fonte-btn"
            variant="danger"
            size="sm"
            onClick={() => handleDelete(row.id_usuario)}
          >
            <MdDelete />
          </Button>
        </div>
      ),
    },
  ];

  const enviarDados = async(dados, editando) => {
    try{
        if(editando){
            const res = await Api.put(`/usuario?email_usuario=${dadosForm.email}`, dados)

            if(res.status === 200){
                toast.success('Usuário editado', {
                    position: "top-right",
                    autoClose: 3000,
                });
                await buscarUsuarios();
                setShowModal(false);
            }
        }else{
            const res = await Api.post('/usuario', dados)
             if(res.status === 201){
                toast.success('Usuário criado', {
                    position: "top-right",
                    autoClose: 3000,
                });
                await buscarUsuarios();
                setShowModal(false);
            }
        }
    }catch(err){
        toast.error(err.response.data || 'Erro ao salvar usuário', {
            position: "top-right",
            autoClose: 3000,
        });
        console.log('Erro ao enviar dados', err)
    }
  }

  return (
    <>
      <h1>Usuários</h1>
      <DadosTable columns={columns} rows={usuarios} keyField={"id_usuario"} />
      <UsuarioModal data={dadosForm} handleClose={() => setShowModal(false)} show={showModal} onSubmit={enviarDados}/>
    </>
  );
}

export default Usuarios;