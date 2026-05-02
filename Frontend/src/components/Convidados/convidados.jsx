import { use, useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import { IoMdAddCircleOutline } from "react-icons/io";
import DadosTable from "../Table/table";
import ModalConvidadoNovo from "../Modais/Convidado/modalConvidadoNovo";
import ConvidadoModalNovo from "../Modais/Convidado/modalConvidadoNovo";
import ConvidadoModalEditar from "../Modais/Convidado/modalConvidadoEditar";
import ConvidadoModalDeletar from "../Modais/Convidado/modalConvidadoDeletar";
import { toast } from 'react-toastify';



function Convidados() {
    const [convidados, setConvidados] = useState([]);
    const [showModalNovo, setShowModalNovo] = useState(false);
    const [showModalEditar, setShowModalEditar] = useState(false);
    const [showModalDeletar, setShowModalDeletar] = useState(false);
    const [dadosForm, setDadosForm] = useState(null);
    const [editando, setEditando] = useState(false)
    const [emailConvidado, setEmailConvidado] = useState("")

    const buscarConvidados = async () => {
        try {
            const res = await Api.get("/convidado");

            setConvidados(res.data.dados);
            console.log(res.data.dados);
        } catch (err) {
            toast.error('Erro ao buscar convidados', {
                position: "top-right",
                autoClose: 3000,
            });
            console.log(err);
        }
    };

    useEffect(() => {
        buscarConvidados();
    }, []);


    const handleEdit = (row) => {
        console.log("Editando", row);
        setDadosForm(row)
        setShowModalEditar(true)
    };

    const handleDelete = async (email) => {
        console.log("Excluindo", email);
        setEmailConvidado(email)
        setShowModalDeletar(true)
    };





    const columns = [
        { header: "Id", accessor: "id_convidado" },
        { header: "Nome", accessor: "nome" },
        { header: "Sobrenome", accessor: "sobrenome" },
        { header: "Email", accessor: "email" },
        { header: "Cpf", accessor: "cpf" },
        { header: "Telefone", accessor: "telefone" },
        { header: "Categoria", accessor: "categoria" },
        { header: "Confirmação", accessor: "confirmacao" },

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
                        onClick={() => handleDelete(row.email)}
                    >
                        <MdDelete />
                    </Button>
                </div>
            ),
        },
    ];

    const enviarDadosNovo = async (dados) => {
        try {

            const res = await Api.post('/convidado', dados)
            if (res.status === 201) {
                toast.success('Convidado criado', {
                    position: "top-right",
                    autoClose: 3000,
                });
                await buscarConvidados()
                setShowModalNovo(false)
            }
        } catch (err) {
            toast.error(err.response.data || 'Erro ao criar convidado', {
                position: "top-right",
                autoClose: 3000,
            });
            console.log('Erro ao enviar dados', err)
        }
    }

    const enviarDadosEditar = async (dados, editando) => {
        try {
            if (editando) {
                const res = await Api.put(`/convidado?email_convidado=${dadosForm.email}`, dados)

                if (res.status === 200) {
                    toast.success('Convidado editado', {
                        position: "top-right",
                        autoClose: 3000,
                    });
                    await buscarConvidados()
                    setShowModalEditar(false)
                }
            }

        } catch (err) {
            toast.error(err.response.data || 'Erro ao editar convidado', {
                position: "top-right",
                autoClose: 3000,
            });
            console.log('Erro ao enviar dados', err)
        }
    }


    const deletarConvidado = async () => {
        console.log(emailConvidado)
        try{
           const res = await Api.delete(`/convidado?email_convidado=${emailConvidado}`)
           if(res.status === 200){
            toast.success('Convidado deletado', {
                position: "top-right",
                autoClose: 3000,
            });
            await buscarConvidados()
            setShowModalDeletar(false)
           }
        }catch(err){
            toast.error(err.response.data || 'Erro ao deletar convidado', {
                position: "top-right",
                autoClose: 3000,
            });
            console.log(err)
        }
    }
    

    return (
        <>
            <h1>Convidados</h1>

            <Button onClick={() => setShowModalNovo(true)} className="my-3 ignorar-fonte-btn" variant="primary">
                <IoMdAddCircleOutline /> Criar novo
            </Button>
            <DadosTable columns={columns} rows={convidados} keyField={"id_convidado"} />
            <ConvidadoModalNovo data={dadosForm} handleClose={() => setShowModalNovo(false)} show={showModalNovo} onSubmit={enviarDadosNovo} />
            <ConvidadoModalEditar data={dadosForm} handleClose={() => setShowModalEditar(false)} show={showModalEditar} onSubmit={enviarDadosEditar} />
            <ConvidadoModalDeletar deletar={deletarConvidado} show={showModalDeletar} handleClose={() => setShowModalDeletar(false)}/> 
        </>
    );
}

export default Convidados;