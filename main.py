from flask import Flask, render_template, request, jsonify,  send_file, url_for,  session, redirect
import src.tools as tools
import os, json, copy, statistics
from json import dumps
import pandas as pd
from flask_cors import CORS, cross_origin
import src.import_xlsx as modelo
import io
from werkzeug.utils import secure_filename


app = Flask(__name__)
CORS(app)
UPLOAD_FOLDER = os.path.join(os.getcwd(), 'upload')

@app.before_first_request
def execute():
    dfclimas_ = pd.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.converter2float(dfclimas)
    nomeclimas = list(set(dfclimas['cidade']))
    nomeclimas.sort()

    dflim = tools.read_csv('db/limites_metamodelo.csv')

    nomeredes = {
        'CgTR': 'network/dnn_OUT_CgTT_Cooling_0.99547_0.4165_11.92.onnx',
        'CgTA': 'network/dnn_OUT_CgTT_Heating_0.99845_0.1747_11.43.onnx',
        'PHsFT': 'network/dnn_OUT_PHFFT_Calor_0.99715_1.7158_8.16.onnx',
        'PHiFT': 'network/dnn_OUT_PHFFT_Frio_0.99955_0.6177_4.68.onnx',
        'TOMax': 'network/dnn_OUT_TOMax_0.99826_0.201_0.76.onnx',
        'TOMin': 'network/dnn_OUT_TOMin_0.99885_0.1647_0.82.onnx'}

    posicoes_array = json.load(open('db/entradas_todos.json'))
    posicoes_array = {int(i): posicoes_array[i] for i in posicoes_array}

    app.secret_key = 'adshuuasdh23293adas'
    app.config['SESSION_TYPE'] = 'filesystem'
    


@app.route('/', methods=['GET', 'POST'])
def index():
    return render_template('index.html')

@app.route('/upload', methods=['GET', 'POST'])
def data():
    
    if request.method == 'POST':
        file = request.files['upload-file']
        save_path = os.path.join(UPLOAD_FOLDER, secure_filename(file.filename))
        file.save(save_path)
        cees = modelo.get_info_xls(save_path)[2]
        nome_uh = modelo.get_info_xls(save_path)[0]
        complementos = {
            'Condicao cobertura': [modelo.get_info_xls(save_path)[4]],
            'Condicao solo': [modelo.get_info_xls(save_path)[3]],
            'zb': [modelo.get_info_xls(save_path)[5]],
            'tipologia': [modelo.get_info_xls(save_path)[6]],
            'area condicionada': [modelo.get_info_xls(save_path)[7]]
        }
        session["complementos"] = complementos

        dataset = modelo.metamodelo(save_path)[0].reset_index()
        dataset.iloc[:, 1:] = dataset.iloc[:, 1:].astype('float64').round(2)
        dataset['UH'] = nome_uh
        df = dataset.to_dict()
        df.update(cees)
        code = modelo.get_info_xls(save_path)[1]

        ##### codigo para validacao da planilha, com "senha" que muda casa ela seja atualizada ######
        if code != '!yxd%ZNb_kPFPDDjVJ#nX!B-8gsh&PfEy4Am%faXr-KURD$HZTK&sKhNmRSG9P3ZEJgw6%?t^Ynx-XTXuCJ=$RCs#PWDktQuhDChtMfWt*UT-D+LnC7#HuM+2n^p$hFH8XhBTL@HvwU2#UAwSg88HXaaC7=XGEq26*&VPqKa*wka5@DJ^A#M@D_^KtZbx-F=gJ9tjD&CX&EerCW?-@tGcAg8RgjEd%6+DhNk+meV%$!Kh_?VNHR&3=t66%_Y+=m5':
            dataset = {
                'ERRO !! ': ['Atenção! A planilha inserida está desatualizada ou foi alterada de maneira incorreta, '
                         'por favor consulte o site do PBE edifica para acessar a planilha atualizada'],
            }
            dataset = pd.DataFrame(dataset)
            df = dataset.to_dict()

        session["df"] = df

        dataset_ref = modelo.metamodelo(save_path)[1].reset_index()
        dataset_ref.iloc[:, 1:] = dataset_ref.iloc[:, 1:].astype('float64').round(2)
        df_ref = dataset_ref.to_dict()
        session["df_ref"] = df_ref

        os.remove(save_path)
        return render_template('resultado_planilha.html')

@app.route('/download_xlsx', methods=['GET', 'POST'])
def download_xlsx():
    df = session.get("df")
    app.secret_key = 'adshuuasdh23293adas'
    app.config['SESSION_TYPE'] = 'filesystem'
    df.pop('CEE aquecimento')
    df.pop('CEE resfriamento')
    nome_uh = df['UH']['0']
    df.pop('UH')
    dataset = pd.DataFrame.from_dict(df)
    in_memory_fp = io.BytesIO()
    dataset.to_excel(in_memory_fp, index=None)
    in_memory_fp.seek(0, 0)
    return send_file(in_memory_fp, download_name=f'Resultados_{nome_uh}.xlsx', as_attachment=True)

@app.route('/info_complementares_upload', methods=['GET'])
def complementares_upload():
    df = session.get("df")
    app.secret_key = 'adshuuasdh23293adas'
    app.config['SESSION_TYPE'] = 'filesystem'
    cee_aq = df['CEE aquecimento']
    cee_resf = df['CEE resfriamento']
    df.pop('CEE aquecimento')
    df.pop('CEE resfriamento')
    df.pop('UH')
    dataset = pd.DataFrame.from_dict(df)
    carga_resf = dataset['Carga_termica_resfriamento'].tolist()
    carga_aquecimento = dataset['Carga_termica_aquecimento'].tolist()
    consumo_resf = round(sum([a/b for a,b in zip(carga_resf,cee_resf)]),2)
    consumo_aqueci = round(sum([a/b for a,b in zip(carga_aquecimento,cee_aq)]),2)
    carga_aquecimento = round(dataset['Carga_termica_aquecimento'].sum(),2)
    carga_resfriamento = round(dataset['Carga_termica_resfriamento'].sum(), 2)
    PHiFT = round(dataset['PHiFT'].mean(), 2)
    PHsFT = round(dataset['PHsFT'].mean(),2)
    tomax = dataset['Tomax'].max()
    tomin = dataset['Tomin'].min()

    contador_quartos = 0
    for i in dataset['APP']:
        if 'Dormitório' in i:
            contador_quartos += 1


    complementos = session.get("complementos")
    cond_cob = complementos['Condicao cobertura'][0]
    cond_solo = complementos['Condicao solo'][0]
    zb = complementos['zb'][0]
    tipologia = complementos['tipologia'][0]
    area_condicionada = complementos['area condicionada'][0]



    ##### ref #########
    df_ref = session.get("df_ref")
    dataset_ref = pd.DataFrame.from_dict(df_ref)
    dataset_ref['CEE aquecimento ref'] = 3.42
    dataset_ref['CEE resfriamento ref'] = 3.5
    cee_aq_ref = dataset_ref['CEE aquecimento ref']
    cee_resf_ref = dataset_ref['CEE resfriamento ref']
    carga_resf_ref = dataset_ref['Carga_termica_resfriamento'].tolist()
    carga_aquecimento_ref = dataset_ref['Carga_termica_aquecimento'].tolist()
    consumo_resf_ref = round(sum([a/b for a,b in zip(carga_resf_ref, cee_resf_ref)]),2)
    consumo_aqueci_ref = round(sum([a/b for a,b in zip(carga_aquecimento_ref,cee_aq_ref)]),2)
    carga_aquecimento_ref = round(dataset_ref['Carga_termica_aquecimento'].sum(),2)
    carga_resfriamento_ref = round(dataset_ref['Carga_termica_resfriamento'].sum(), 2)
    PHiFT_ref = round(dataset_ref['PHiFT'].mean(), 2)
    PHsFT_ref = round(dataset_ref['PHsFT'].mean(),2)
    tomax_ref = dataset_ref['Tomax'].max()
    tomin_ref = dataset_ref['Tomin'].min()


    lista = [consumo_resf, consumo_aqueci, carga_resfriamento, carga_aquecimento, PHiFT, PHsFT, tomax, tomin, contador_quartos, cond_cob, cond_solo, zb, tipologia, area_condicionada]
    lista_ref = [consumo_resf_ref, consumo_aqueci_ref, carga_resfriamento_ref, carga_aquecimento_ref, PHiFT_ref,
                 PHsFT_ref, tomax_ref, tomin_ref]

    #passar uma lista com todas as variáveis existentes no template
    return render_template('info_complementares_upload.html', lista=lista, lista_ref=lista_ref)

@app.route('/interface')
def interface():
    dfclimas_ = pd.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.converter2float(dfclimas)
    nomeclimas = list(set(dfclimas['cidade']))
    nomeclimas.sort()

    return render_template('interface.html', nomeclimas=nomeclimas)

@app.route('/info_complementares', methods=['GET'])
def complementares():
    return render_template('info_complementares.html')

@app.route('/resultado_interface')
def classificacao_final():
    return render_template('resultado_classificacao.html')

@app.route(f"/default/calc/<data>")
def calc(data):
    data = data.split('|')[:-1]
    data = [i.split('=') for i in data]
    data = {i[0]: i[1] for i in data}
    data = tools.converter2float(data)
    in2use = tools.get_in2use(data)
    area_piso = in2use['APP_FloorArea']
    indices_clima = tools.get_indices_clima(dfclimas, data)
    entradas_metamodelos = []
    entradas_metamodelos_ref = []
    complementos = tools.get_complementos(data)[0]
    ceer = complementos['CEEr'] if complementos['CEEr'] > 0 else 1
    ceea = complementos['CEEa'] if complementos['CEEa'] > 0 else 1

    tipologia = tools.get_complementos(data)[1]  # 1 é unifamiliar e 2 é multi
    area_condicionada = data['aTotAPP']
    cond_cob = 1 if data['cob'] == "sol" else 0
    cond_piso = 1 if data['piso'] == "solo" else 0

    for month in range(1, 13):
        in2use_copy = copy.deepcopy(in2use)
        ij = tools.get_indice_ij_clima(indices_clima, dfclimas, month)
        in2clima = tools.get_in2clima(dfclimas, ij)
        in2use_copy.update(in2clima)
        zona_bioclimatica = tools.get_ZB(dfclimas, ij)['zona_bioclimatica']
        in2use_ref = copy.deepcopy(in2use_copy)
        in2use_ref = tools.get_dicio_ref(data, in2use_ref, zona_bioclimatica)
        in2use_ref = {nome_col: tools.normalizar_maxmin(in2use_ref[nome_col], nome_col, dflim) for nome_col in
                      in2use_ref}
        in2use_ref = {posicoes_array[i]: in2use_ref[posicoes_array[i]] for i, _ in enumerate(in2use_ref.keys())}
        entradas_metamodelos_ref.append(list(in2use_ref.values()))

        in2use_copy = {nome_col: tools.normalizar_maxmin(in2use_copy[nome_col], nome_col, dflim) for nome_col in
                       in2use_copy}
        in2use_copy = {posicoes_array[i]: in2use_copy[posicoes_array[i]] for i, _ in enumerate(in2use_copy.keys())}
        entradas_metamodelos.append(list(in2use_copy.values()))

    CgTR, CgTA, PHsFT, PHiFT, TOMax, TOMin = tools.run_models(nomeredes, entradas_metamodelos)
    CgTR = CgTR * area_piso
    CgTA = CgTA * area_piso
    Consumo_resfriamento = CgTR / ceer
    clima_tbs = in2clima['CLIMA_TBSm']
    Consumo_aquecimento = (CgTA / ceea)

    CgTR_ref, CgTA_ref, PHsFT_ref, PHiFT_ref, TOMax_ref, TOMin_ref = tools.run_models(nomeredes,
                                                                                      entradas_metamodelos_ref)
    CgTR_ref = CgTR_ref * area_piso
    CgTA_ref = CgTA_ref * area_piso
    Consumo_resfriamento_ref = CgTR_ref / 3.5
    Consumo_aquecimento_ref = (CgTA_ref / ceea)
    ambiente = in2use_copy['CARAC_ambiente_1']

    return dumps({
        'Consumo_aquecimento': f'{sum(Consumo_aquecimento)}' if (
                    sum(Consumo_aquecimento) >= 0 and clima_tbs < 25) else f'{0}',
        'Consumo_resfriamento': f'{sum(Consumo_resfriamento)}' if sum(Consumo_resfriamento) >= 0 else f'{0}',
        'PHsFT': f'{statistics.mean(PHsFT)}' if statistics.mean(PHsFT) >= 0 else f'{0}',
        'PHiFT': f'{statistics.mean(PHiFT)}' if (statistics.mean(PHiFT) >= 0 and clima_tbs < 25) else f'{0}',
        'PHFT': f'{100 - statistics.mean(PHsFT) - (statistics.mean(PHiFT) if (statistics.mean(PHsFT) >= 0 and clima_tbs < 25) else 0)}',
        'CgTR': f'{sum(CgTR)}' if sum(CgTR) >= 0 else f'{0}',
        'CgTA': f'{sum(CgTA)}' if (sum(CgTA) >= 0 and clima_tbs < 25) else f'{0}',
        'TOMax': f'{max(TOMax)}', 'TOMin': f'{min(TOMin)}',
        'TBSm': f'{clima_tbs}',
        'Consumo_aquecimento_ref': f'{sum(Consumo_aquecimento_ref)}' if (
                    sum(Consumo_aquecimento_ref) >= 0 and clima_tbs < 25) else f'{0}',
        'Consumo_resfriamento_ref': f'{sum(Consumo_resfriamento_ref)}' if sum(
            Consumo_resfriamento_ref) >= 0 else f'{0}',
        'PHsFT_ref': f'{statistics.mean(PHsFT_ref)}' if statistics.mean(PHsFT_ref) >= 0 else f'{0}',
        'PHiFT_ref': f'{statistics.mean(PHiFT_ref)}' if (
                    statistics.mean(PHiFT_ref) >= 0 and clima_tbs < 25) else f'{0}',
        'PHFT_ref': f'{100 - statistics.mean(PHsFT_ref) - (statistics.mean(PHiFT_ref) if (statistics.mean(PHiFT_ref) >= 0 and clima_tbs < 25) else 0)}',
        'CgTR_ref': f'{sum(CgTR_ref)}' if sum(CgTR_ref) >= 0 else f'{0}',
        'CgTA_ref': f'{sum(CgTA_ref)}' if (sum(CgTA_ref) >= 0 and clima_tbs < 25) else f'{0}',
        'TOMax_ref': f'{max(TOMax_ref)}', 'TOMin_ref': f'{min(TOMin_ref)}',
        'ambiente': f'{ambiente}',
        'tipologia': f'{tipologia}',
        'area_condicionada': f'{area_condicionada}',
        'zona_bioclimatica': f'{zona_bioclimatica[2:]},',
        'cond_cob': f'{cond_cob}',
        'cond_piso': f'{cond_piso}'
    })


@app.route(f'/nome_climas', methods=['GET'])
def get_climas_ann_v3():
    dfclimas_ = pd.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.read_csv('db/baseweb_inmet.csv')
    dfclimas = tools.converter2float(dfclimas)
    nomeclimas = list(set(dfclimas['cidade']))
    nomeclimas.sort()
    return jsonify(nomeclimas)



    
# map(a.__getitem__, b)
